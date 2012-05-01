<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 */

class Core_Model_Contact extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';
    const STATUS_CONVERT = 'CONVERT';


    /**
     * Self service account
     */
    const SELF_SERVICE_ENABLED = 1;
    const SELF_SERVICE_DISABLED = 0;

    const SELF_SERVICE_ACTIVE = 1;
    const SELF_SERVICE_BLOCKED = 0;

    /**
     * @TODO marked for deprecation
     */
    public $db;

    /**
     * @TODO marked for deprecation use _contactId instead
     */
    protected $_contact_id;


    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Contact';

    /**
     * @var array the default observers
     */
    protected $_defaultObservers = array(
        'Core_Model_Contact_Notify_Email'
    );


    /**
     * @var whether or not to check ACL
     * @TODO marked for deprecation perform access checks for REST in services
     */
    protected $_accessCheck = true;

    /**
     * @var Password of the self service client application
     */
    protected $_selfServiceAccountPassword;
   
    /**
     * @param int $contactId the contact id to operate on
     */
    public function __construct($contactId = null)
    {
        $this->db = Zend_Registry::get('db');
        if (is_numeric($contactId)) {
            $this->_contact_id = $contactId;
        }
        parent::__construct($contactId);
    }

    /**
     * @param int the id of the contact
     * @deprecated use setContactId() instead
     */
    public function setId($id)
    {
        $this->_contact_id = $id;
        return $this;
    }

    /**
     * Set the contact ID
     * @param int $contactId the contact ID
     */
    public function setContactId($contactId)
    {
        if (!is_numeric($contactId)) {
            throw new Exception('Contact ID must be an integer');
        }

        $this->_contact_id = $contactId;
        return $this;
    }

    /**
     * @return int contact ID 
     */
    public function getContactId()
    {
       return $this->_contact_id;
    }

    /**
     * @param bool $toCheck
     * @return fluent interface
     */
    public function setAccessCheck($toCheck)
    {
        $this->_accessCheck = $toCheck;
        return $this;
    }

    /**
     * Creates a row in the contact table
     * @param array $data
     * @return int contact ID
     */
    public function create($data = array())
    {
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        $data['created'] = time();
        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }
        
        $result = parent::create($data);  
        
        $table = $this->getTable();
        $this->_contact_id = $result;
        $this->setStatus(self::STATUS_CREATE);
        
        $log = $this->getLoggerService();
        $info = 'Contact created with contact id = '. $this->_contact_id;
        $log->info($info);
        
        return $this->_contact_id;
    }


    /**
     * @return result object of Zend_Db_Select 
     * Bassed on the contactId
     * @param string $criteria 
     * @param array $params of the criteria
     */
    public function fetch($criteria = null, $params = null)
    {
        if (!$this->_accessCheck) {
            $table = $this->getTable();
            $select = $table->select();
            if ($criteria = 'byEmail') {
                $select->where('work_email = ?', $params['work_email']);
                $result = $table->fetchRow($select);
                if ($result) {
                    $returnValue = $result->toArray();
                } else {
                    $returnValue = null;
                }
            }
            return $returnValue;
        } 

        $select = $this->db->select();
        $select->from(array('c'=>'contact'), array('*'))
                ->joinLeft(array('s'=>'salutation'),
                        'c.salutation_id = s.salutation_id', array('s'=>'name as salutation'))
                ->joinLeft(array('u'=>'user'),
                    'c.assigned_to = u.user_id', array('u.email'=>'email as assignedToEmail'))
                ->joinLeft(array('ca'=>'contact'),
                        'c.assistant_id = ca.contact_id', array('ca'=>'assistant_id as assist', 
                    'ca.first_name as assistantFirstName', 'ca.middle_name as assistantMiddleName', 
                    'ca.last_name as assistantLastName'))
                ->joinLeft(array('cr'=>'contact'),
                        'c.reports_to = cr.contact_id', array('cr'=>'reports_to as reportsToId', 
                    'cr.first_name as reportsToFirstName', 'cr.middle_name as reportsToMiddleName', 
                    'cr.last_name as reportsToLastName'))
                ->joinLeft(array('b'=>'branch'),
                    'c.branch_id = b.branch_id', array('b.branch_name'))
                ->joinLeft(array('a'=>'account'),
                    'c.account_id = a.account_id', array('a.account_name'))
                ->where('c.contact_id = ?', $this->_contact_id)
                ;

        $result = $this->db->fetchRow($select);
       
        return $result;
    }

     /**
     * Import leads from a CSV file
     *
     * @param array $metaData lead meta data like source, status ,etc
     * @param string $location the location of the CSV file.
     *
     * @return void
     */
    public function import($metaData, $location)
    {
        $form = new Core_Form_Contact_Create;
        $table = $this->getTable();
        $noOfAffectedRows = 0;
        $success = null;
        $handle = fopen($location, "r");

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $dataToImport = array(
                'first_name'                =>  $data[0],
                'middle_name'               =>  $data[1],
                'last_name'                 =>  $data[2],
                'work_phone'                =>  $data[3],
                'home_phone'                =>  $data[4],
                'mobile'                    =>  $data[5],
                'fax'                       =>  $data[6],
                'title'                     =>  $data[7],
                'department'                =>  $data[8],
                'work_email'                =>  $data[9],
                'other_email'               =>  $data[10],
                'do_not_call'               =>  $data[11],
                'email_opt_out'             =>  $data[12],
                'billing_city'              =>  $data[13],
                'billing_state'             =>  $data[14],
                'billing_postal_code'       =>  $data[15],
                'billing_country'           =>  $data[16],
                'shipping_city'             =>  $data[17],
                'shipping_state'            =>  $data[18],
                'shipping_postal_code'      =>  $data[19],
                'shipping_country'          =>  $data[20],
                'description'               =>  $data[21],
                'reports_to'                =>  $data[22],
                'salutation_id'             =>  $data[23],
                'assistant_id'              =>  $data[24],
                'birthday'                  =>  $data[25],
                'assigned_to'               =>  $data[26],
                'created_by'                =>  $data[27],
                'created'                   =>  $data[28],
                'updated'                   =>  $data[29],
                'branch_id'                 =>  $data[30],
                'account_id'                =>  $data[31],
                'billing_address_line_1'    =>  $data[32],
                'billing_address_line_2'    =>  $data[33],
                'billing_address_line_3'    =>  $data[34],
                'billing_address_line_4'    =>  $data[35],
                'shipping_address_line_1'   =>  $data[36],
                'shipping_address_line_2'   =>  $data[37],
                'shipping_address_line_3'   =>  $data[38],
                'shipping_address_line_4'   =>  $data[39],
                'ss_enabled'                =>  $data[40],
                'ss_active'                 =>  $data[41],
                'ss_password'               =>  $data[42],
                'ledger_id'                 =>  $data[43],
                'campaign_id'               =>  $data[44]
            );
            $mergedData = array_merge($dataToImport, $metaData);
            $result = array_pop($mergedData);
            
            if ($form->isValid($mergedData)) {
                $success = $table->insert($mergedData);  
                ++$noOfAffectedRows;
            } 
        }
        fclose($handle); 
        if ($success) {
            return $noOfAffectedRows;
        }
    } 

    /**
     * Zend_Db_Select object for index page
     * @TODO marked for deprecation
     * Use Core_Model_Contact_Index instead
     */
    public function getIndexSelectObject()
    {
        $user = $this->getCurrentUser();
        $select = $this->db->select();
        $select->from(array('c' => 'contact'),
                  #array('c.contactId', 'c.firstName', 'c.lastName', 'c.mobile'))
                  array('c.*'))
                ->joinLeft(array('a'=>'account'),
                    'c.account_id = a.account_id', array('a.account_name'))
                ->joinLeft(array('u'=>'user'), 'u.user_id = c.assigned_to', array('u.email as assignedToEmail'))
                ->joinLeft(array('b'=>'branch'), 'b.branch_id = c.branch_id', array('b.branch_name'))
                ->joinLeft(array('p'=>'profile'), 'p.user_id = c.assigned_to', null);
        /**
         * Apply ACLs
         */
        $acl = Zend_Registry::get('acl');
        if ($acl->isAllowed($user, 'view all contacts')) {
        } elseif ($acl->isAllowed($user, 'view own branch contacts')) {
            $select->where('c.branch_id = ?', Core_Model_User_Current::getBranchId());
        } elseif ($acl->isAllowed($user, 'view own role contacts')) {
            $select->where('p.primary_role = ?', Core_Model_User_Current::getPrimaryRoleId());
        } elseif ($acl->isAllowed($user, 'view own contacts')) {
            $select->where('c.assigned_to = ?', $this->getCurrentUser()->getUserId());
        } else {
            $select->where('1>2');
        }

        return $select;
    }

    /**
     * Build the Zend_Db_Select object
     * @return $select
     * @TODO marked for deprecation
     * Use Core_Model_Contact_Index instead
     */
    public function getfetchAllSelectObject()
    {
        $select = $this->db->select();
        $select->from(array('c'=>'contact'), array('*'))
                ->joinLeft(array('s'=>'salutation'),
                        'c.salutation_id = s.salutation_id', array('s'=>'name as salutation'))
                ->joinLeft(array('u'=>'user'),
                    'c.assigned_to = u.user_id', array('u.email'=>'email as assignedToEmail'))
                ->joinLeft(array('ca'=>'contact'),
                        'ca.assistant_id = c.contact_id', array('ca'=>'assistantId as assist', 
                    'ca.first_name as assistantFirstName', 'ca.middle_name as assistantMiddleName', 
                    'ca.last_name as assistantLastName'))
                ->joinLeft(array('b'=>'branch'),
                    'c.branch_id = b.branch_id', array('b.branchName'))
                ->joinLeft(array('a'=>'account'),
                    'c.account_id = a.account_id', array('a.accountName'))
                ;
        return $select;

    }

    /**
     * @return result array from Zend_Db_Select 
     * based on the contactId
     * @TODO marked for deprecation cast to array in client code
     */
    public function fetchAsArray()
    {
        $result = $this->fetch();
        $result = (array) $result;    
        return $result;
    }

    /**
     * Updates the row in the contact table
     * @param array $data 
     * @return bool whether or not the update was successful
     */
    public function edit($data = array())
    {
        $data['updated'] = time();
        $data = $this->unsetNonTableFields($data);
        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }

        $contactId = $this->_contact_id;
        $contactId = $this->db->quote($this->_contact_id, 'INTEGER');
        $this->prepareEphemeral();
        $result = $this->db->update('contact', $data, "contact_id = $contactId");
        $this->setStatus(self::STATUS_EDIT);
        
        $log = $this->getLoggerService();
        $info = 'Contact edited with contact id = '. $this->_contact_id;
        $log->info($info);
        
        return $result;
 
    }
    
    /**
     * Deletes the row in the contact table
     * @return bool whether or not the deletion was successful
     */
    public function delete()
    {
        $this->prepareEphemeral();
        $where = $this->db->quote($this->_contact_id, 'INTEGER');
        $result = $this->db->delete('contact', "contact_id = $where");
        $this->setStatus(self::STATUS_DELETE);
        
        $log = $this->getLoggerService();
        $info = 'Contact deleted with contact id = '. $this->_contact_id;
        $log->info($info);
        
        return $result;
    }

    /**
     * Filter the input submitted by user
     * @TODO marked for deprecation
     * setIgnore() on the appropriate form elements
     */
    public function filterInput($data)
    {
       
        /*
         * Submit key value pair causes problems to Zend_Db::insert
         */
        unset($data['submit']);                           
        unset($data['Submit']);                          

        /*
         * some db table columns must be null if not integer
         */ 
        if (!is_numeric($data['reports_to'])){
            $data['reports_to'] = null;
        }        
 
        if (!is_numeric($data['salutation_id'])){
            $data['salutation_id'] = null;
        }         

        if (!is_numeric($data['assistant_id'])){
            $data['assistant_id'] = null;
        }         

        if (!is_numeric($data['account_id'])){
            $data['account_id'] = null;
        }         

        return $data;

    }   

    /**
     * Initiate index search processing object
     * @return Zend_Db_Select object
     * @TODO marked for deprecation
     * Use Core_Model_Contact_Index instead
     */
    public function getListingSelectObject($search, $sort)
    {
        $process = new Core_Model_Contact_Index;
        $processed = $process->getListingSelectObject($search, $sort);
        return $processed;
    }


    /**
     * Generate Dojo data to assign contact to user
     * @TODO marked for deprecation use user/jsonstore instead. 
     * Simplify the feature
     */
    public function getAssignedToDojoData()
    {
        $contactData = new Core_Model_Contact_Data; 
        $dojoData = $contactData->getAssignedToDojoData();   
        return $dojoData;
    }

    /**
     * Generate Dojo data to assign contact to branch
     * @TODO marked for deprecation. Use branch/jsonstore instead
     * Simplify the feature
     */
    public function getAssignedToBranchDojoData()
    {
        $contactData = new Core_Model_Contact_Data; 
        $dojoData = $contactData->getAssignedToBranchDojoData();   
        return $dojoData;
    }

    /**
     * @return bool whether or not the self service feature is
     * enabled for this contact
     */
    public function getSelfServiceAccountStatus()
    {
        $row = $this->fetch();
        if ($row->ss_enabled == self::SELF_SERVICE_ENABLED) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Enable self service to the contact
     * @return fluent interface
     */
    public function enableSelfService()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('contact_id = ?', $this->_contact_id); 
        $password = substr(
            md5($this->getFullName() . time()),
            0, 7);

        $this->_selfServiceAccountPassword = $password;

        $data = array(
            'ss_enabled' => self::SELF_SERVICE_ENABLED,
            'ss_password' => md5($password),
        );
        $table->update($data, $where);
        $this->nofiySelfServiceAccountCreated();
        return $this;
    }
    
    /**
     * Enable self service to the contact
     * @return fluent interface
     */
    public function disableSelfService()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('contact_id = ?', $this->_contact_id); 
        $data = array(
            'ss_enabled' => self::SELF_SERVICE_DISABLED
        );
        $table->update($data, $where);
        return $this;
    }

    /**
     * Notify the contact that they can now access the self service portal
     */
    public function nofiySelfServiceAccountCreated()
    {
        $organizationModel = new Core_Model_Org;
        $organizationName = $organizationModel->getName();
    
        $clientApplication = new Core_Model_WebService;
        $selfServiceClientApplicationUrl = $clientApplication->getSelfServiceUrl();
        $password = $this->_selfServiceAccountPassword;

        $username = $this->getEmail();

        $message = sprintf(
            "Hello %s,\n\nA self service account has been created for you. " 
            . "You can now logon to %s with the following credentials:" 
            . "\n\nURL: %s \nusername: %s \npassword: %s\n", 
            $this->getFullName(), 
            $organizationName, 
            $selfServiceClientApplicationUrl, 
            $username, 
            $password
        ); 

        $mail = new Core_Service_Mail;
        $mail->addTo($username);
        $mail->setSubject($organizationName 
                . " self service system account created");
        $mail->setBodyText($message);
        try  {  
            $mail->send();
        }   
        catch (Zend_Exception $e) {
            $log = new Core_Service_Log;
            $info = 'Failed in connecting mail server';
            $log->info($info);
        }
    }

    /**
     * @return string full name of the contact
     */
    public function getFullName()
    {
        $contactData = $this->fetch(); 
        if ($contactData) {   
            $firstName = $contactData->first_name;
            $middleName = $contactData->middle_name;
            $lastName = $contactData->last_name;
        }
        else {
            return;
        }

        if ($middleName) {
            return $firstName . ' ' . $middleName . ' ' . $lastName;
        } else {
            return $firstName . ' ' . $lastName;
        }
    }
    
    /**
     * @return string the work email of the contact
     * @TODO marked for deprecation
     * Use getWorkEmail() instead
     */
    public function getEmail()
    {
        $contactData = $this->fetch();
        return $contactData->work_email;
    }

    /**
     * @param string $workEmail the work email address of the contact
     * @return array Zend_Db_Table_Rowset 
     */
    public function findByWorkEmail($workEmail)
    {
        $table = $this->getTable();
        $select = $table->select()->where('work_email = ?', $workEmail);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;

    }

    /**
     * @return object Core_Model_Contact_Notes
     */
    public function getNotes()
    {
        $notes = new Core_Model_Contact_Notes();
        $notes->setModel($this);
        return $notes;
    }

    /**
     * @return string the work email
     */
    public function getWorkEmail()
    {
        $record = $this->fetch();
        return $record->work_email;
    }
    
    public function getLedgerId()
    {
       $data = $this->fetch();
       if($data->ledger_id == "")
       {
            $financeGroupModel = new Core_Model_Finance_Group;
                     
            $ledgerDataToInsert = array (
                    'name' => $this->getFullName(),
                    'fa_group_id' => 
                        $financeGroupModel->getGroupIdByName('Sundry Debtors'),
                    'opening_balance_type' => "1",
                    'opening_balance' => "0",
                );
            $financeLedgerModel = new Core_Model_Finance_Ledger();
            $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
            $dataToUpdate['ledger_id'] = $financeLedgerId;
            $contactId = $this->_contact_id;
            $result = $this->db->update('contact', $dataToUpdate, "contact_id = $contactId");
            return $financeLedgerId;
       }
       return $data->ledger_id;
    }
    
    /**
     * @return initialized ledgerId
     */
    public function initializeLedger($data)
    {
        $financeGroupModel = new Core_Model_Finance_Group;
        $ledgerDataToInsert = array (
                    'name' => $this->getFullName(),
                    'fa_group_id' => 
                        $financeGroupModel->getGroupIdByName('Sundry Debtors'),
                    'opening_balance_type' => $data['opening_balance_type'],
                    'opening_balance' => $data['opening_balance'],
                );
         $financeLedgerModel = new Core_Model_Finance_Ledger();
         $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
         
         $dataToUpdate['ledger_id'] = $financeLedgerId;
         $contactId = $this->_contact_id;
         $result = $this->db->update('contact', $dataToUpdate, "contact_id = $contactId");
         
         return $financeLedgerId;
    }
    
    /**
     * @return bool
     */
    public function ledgerExists()
    {
        $contactRecord = $this->fetch();
        if ($contactRecord->ledger_id == ""){
            return true;
        }
        else {
            return false;
        }
        
    }

    /**
     * @param int campaignId
     * @return array the contacts record with campaignId
     */
    public function getContactsByCampaignId($campaignId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('campaign_id = ?', $campaignId);

        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
 
    }
    
    /**
     * Fetch contacts for dashlets
     */
    public function getContacts()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);

        $select->from(array('c' => 'contact'),
            array('c'=>'*'))
            ->where('assigned_to = ?', $this->getCurrentUser()->getUserId())
            ->order(array('created DESC'))
            ->limit(5, 0);

        $result = $table->fetchAll($select);
        return $result;        
    }
    
    /**
     * @return @Zend_Pagintor object
     */
    public function getSelfServicePaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_SelfService';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }

}



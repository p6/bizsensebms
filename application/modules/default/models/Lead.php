<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation,  version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category BizSense
 * @package  Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Lead extends Core_Model_Abstract
{
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';
    const STATUS_CONVERT = 'CONVERT';


    const VARIABLE_KEY_DEFAULT_ASSIGNEE_ID = 'core_default_lead_assignee_id';

    public $db;
    protected $_lead;
    protected $_leadId;
    protected $_status = '';

    /**
     * Lead fetched object before it was deleted or edited
     */
    protected $_previousLeadData;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Lead';
 

    public function __construct($leadId = null)
    {
        if (is_numeric($leadId)) {  
            $this->_leadId = $leadId;
        }
    }

    /**
     * Sets the lead status upon CRUD actions
     * @param CONST STATUS_?
     * Calls Lead_Notify_Email::update
     */
    public function setStatus($status = null)
    {
        $this->_status = $status;      
        $this->_notify(); 
    }
   
    public function getPreviousLeadData()
    {
        return $this->_previousLeadData;
    }
    
    protected function _notify()    
    {
        $status = $this->_status;
        $emailNotify = new Core_Model_Lead_Notify_Email();
        $emailNotify->update($this);    

    }
        
    public function getStatus()
    {
        return $this->_status;
        
    }            

    /**
     * @deprecated use setLeadID() instead
     */
    public function _setId($leadId = null)
    {
        if (is_numeric($leadId)) {  
            $this->_leadId = $leadId;
        }
    }

    /**
     * Set the lead ID
     * @param int $leadId
     */
    public function setLeadId($leadId)
    {
        if (!is_numeric($leadId)) {  
            throw new Exception('Lead ID must be an integer');
        }
        $this->_leadId = $leadId;
        return $this;   
    }

    /**
     * Get the lead ID
     * @return int $leadId
     */
    public function getLeadId()
    {
        return $this->_leadId;
    }


    /**
     * Creates a row in the lead table
     * @param array $data to be stored
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        /**
         * Add the log columns  
         */
        $data['created'] =  time();
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }
        $leadId = $table->insert($data);            
        $this->_leadId = $leadId;
        $this->setStatus(self::STATUS_CREATE);     
        
        $log = $this->getLoggerService();
        $info = 'Lead created with lead id = '. $this->_leadId;
        $log->info($info);
           
        return $this->_leadId;
    }


    /**
     * Feteches a record from the lead table
     * @return result object from Zend_Db_Select object
     */
    public function fetch()
    {
        $table = $this->getTable();

        $leadId = $this->_leadId;
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(
                    array('l' => 'lead'),
                    array('l' => '*')
                )
                ->joinLeft(
                    array('ls'=>'lead_source'),
                    'l.lead_source_id = ls.lead_source_id', 
                    array('ls.name'=>'name as source')
                )
                ->joinLeft(
                    array('lst'=>'lead_status'),
                    'l.lead_status_id = lst.lead_status_id', 
                    array('lst.name'=>'name as status')
                )
                ->joinLeft(
                    array('u'=>'user'),
                    'l.assigned_to = u.user_id', 
                    array('u.email'=>'email as assigned_to_email')
                )
                ->joinLeft(
                    array('b'=>'branch'),
                    'l.branch_id = b.branch_id', array('b.branch_name')
                )
                ->where('lead_id = ?', $leadId);

        $result = $table->fetchRow($select);
        if($result) {
            $result = $result->toArray();
        }
        return $result;
    }


   /**
    * Updates the row in the lead table
    */ 
    public function edit($data = array()) 
    {
        $table = $this->getTable();
        $leadId = $this->_leadId;
        $data['updated'] = time();
        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }
        $this->_previousLeadData = $this->fetch();
        $data = $this->unsetNonTableFields($data);
        $where = $table->getAdapter()->quoteInto('lead_id = ?', $leadId);
        $result = $table->update($data, $where);
        $this->setStatus(self::STATUS_EDIT);  
        
        $log = $this->getLoggerService();
        $info = 'Lead edited with lead id = '. $this->_leadId;
        $log->info($info);
          
        return $result;
    }    

    /**
     * Convert lead to contact, opportunity and account
     */
    public function convert($data)
    {
        /**
         * Mark the lead as converted and copy data to account table
         */
        $accountTable = new Core_Model_DbTable_Account;
        $lead = $this->fetch();        
        $accountData = array(
            'account_name' => $data['account_name'],
            'billing_address_line_1' => 
                $data['account_billing_address_line_1'],
            'billing_address_line_2' => 
                $data['account_billing_address_line_2'],
            'billing_address_line_3' => 
                $data['account_billing_address_line_3'],
            'billing_address_line_4' => 
                $data['account_billing_address_line_4'],
            'billing_city' => 
                $data['account_billing_city'],
            'billing_state' => 
                $data['account_billing_state'],
            'billing_postal_code' => 
                $data['account_billing_postal_code'],
            'billing_country' => 
                $data['account_billing_country'],
            'shipping_address_line_1' => 
                $data['account_shipping_address_line_1'],
            'shipping_address_line_2' => 
                $data['account_shipping_address_line_2'],
            'shipping_address_line_3' => 
                $data['account_shipping_address_line_3'],
            'shipping_address_line_4' => 
                $data['account_shipping_address_line_4'],
            'shipping_city' => $data['account_shipping_city'],
            'shipping_state' => $data['account_shipping_state'],
            'shipping_postal_code' => $data['account_shipping_postal_code'],
            'shipping_country' => $data['account_shipping_country'],
            'assigned_to' => $lead['assigned_to'], 
            'branch_id' => $data['branch_id'], 
            'phone' => $lead['work_phone'],
            'email' => $lead['email'],
            'fax' => $lead['fax'],             
            'mobile' => $lead['mobile'],             
            'description' => $lead['description'],             
            'created' => time(),
          );
            
        $accountId = $data['account_id'];
        if ($data['to_account'] == "1") {
            $accountResult = $accountTable->insert($accountData);
            $accountId = $accountResult;
        } else {
            $accountId = null;
        }    

        /**
         * create contact from lead 
         */
         $contactTable = new Core_Model_DbTable_Contact;
         $contactData = array(
            'first_name' =>  $lead['first_name'],
            'last_name' =>  $lead['last_name'],
            'fax' =>  $lead['fax'],
            'mobile' =>  $lead['mobile'],
            'home_phone' =>  $lead['home_phone'],
            'work_phone' =>  $lead['work_phone'],
            'work_email' =>  $lead['email'],
            'do_not_call' =>  $lead['do_not_call'],
            'email_opt_out' =>  $lead['email_opt_out'],
            'billing_address_line_1' =>  
                $data['contact_billing_address_line_1'],
            'billing_address_line_2' =>  
                $data['contact_billing_address_line_2'],
            'billing_address_line_3' =>  
                $data['contact_billing_address_line_3'],
            'billing_address_line_4' =>  
                $data['contact_billing_address_line_4'],
            'billing_city' =>  $data['contact_billing_city'],
            'billing_state' =>  $data['contact_billing_state'],
            'billing_country' =>  $data['contact_billing_country'],
            'billing_postal_code' =>  
                $data['contact_billing_postal_code'],
            'shipping_address_line_1' =>  
                $data['contact_shipping_address_line_1'],
            'shipping_address_line_2' =>  
                $data['contact_shipping_address_line_2'],
            'shipping_address_line_3' =>  
                $data['contact_shipping_address_line_3'],
            'shipping_address_line_4' => 
                $data['contact_shipping_address_line_4'],
            'shipping_city' =>  $data['contact_shipping_city'],
            'shipping_state' =>  $data['contact_shipping_state'],
            'shipping_postal_code' =>  $data['contact_shipping_postal_code'],
            'shipping_country' =>  $data['contact_shipping_country'],
            'description' =>  $lead['description'],
            'assigned_to' =>  $lead['assigned_to'],
            'branch_id' =>  $lead['branch_id'],
            'account_id' =>  $accountId,
            'created' => time(),
          );
          $contactResult = $contactTable->insert($contactData);
          $contactId = $contactResult; 
            
          /**
           * Change the converted flag in lead table
           */
          $convertedData = array (
            'converted' =>  1
          );
          $where = "lead_id = " . $this->_leadId;
           $table = $this->getTable();
          $table->update($convertedData, $where);


          /**
           * Copy table to opportunity 
           */
        if (is_numeric($data['lead_source'])) {
            $leadSourceId = $data['lead_source'];
        } else {
            $leadSourceId = null;
        }

        if (is_numeric($data['sales_stage'])) {
            $salesStageId = $data['sales_stage'];
        } else {
            $salesStageId = null;
        }

        $opportunityTable = new Core_Model_DbTable_Opportunity;
        $opportunityData = array(
            'name' =>  $data['opportunity'],
            'amount' =>  $data['opportunity_value'],
            'expected_close_date' =>  $data['expected_close_date'],
            'assigned_to' => $lead['assigned_to'],
            'lead_source_id' => $leadSourceId,
            'sales_stage_id' => $salesStageId,
            'account_id' => $accountId,
            'contact_id' => $contactId,
            'description' => $lead['description'],
            'branch_id' => $data['branch_id'],
            'created' => time(),
        );  

        if ($data['to_opportunity'] == '1') {
            $opportunityTable->insert($opportunityData);
        }    
 
    }    

    /** 
     * Deletes a row in the lead table
     */
    public function delete()
    {
        $this->prepareEphemeral();
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('lead_id = ?', $this->_leadId);
        $result = $table->delete($where);
        $this->setStatus(self::STATUS_DELETE);    
        
        $log = $this->getLoggerService();
        $info = 'Lead deleted with lead id = '. $this->_leadId;
        $log->info($info);
        
        return $result;
    }

    /**
     * Filter the input submitted by user
     */
    public function filterInput($data)
    {
        /**
         * Submit key value pair causes problems to Zend_Db::insert
         */
        unset($data['submit']);
        unset($data['Submit']);
 
        return $data;
    }

    /**
     * Generate Dojo data to assign lead to user
     *
     * @return Zend_Dojo_Data object
     */
    public function getAssignedToDojoData()
    {
        $leadData = new Core_Model_Lead_Data; 
        $dojoData = $leadData->getAssignedToDojoData();   
        return $dojoData;
    }

    /**
     * Generate Dojo data to assign lead to branch
     *
     * @return Zend_Dojo_Data object
     */
    public function getAssignedToBranchDojoData()
    {
        $leadData = new Core_Model_Lead_Data; 
        $dojoData = $leadData->getAssignedToBranchDojoData();   
        return $dojoData;
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
        $form = new Core_Form_Lead_Create;
        $table = $this->getTable();
        static $noOfAffectedRows = 0;
        $success = null;
        $handle = fopen($location, "r");

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $dataToImport = array(
                'first_name'        =>  $data[0],
                'middle_name'       =>  $data[1],
                'last_name'         =>  $data[2],
                'company_name'      =>  $data[3],
                'home_phone'        =>  $data[4],
                'work_phone'        =>  $data[5],
                'mobile'            =>  $data[6],
                'fax'               =>  $data[7],
                'email'             =>  $data[8],
                'address_line_1'    =>  $data[9],
                'address_line_2'    =>  $data[10],
                'address_line_3'    =>  $data[11],
                'address_line_4'    =>  $data[12],
                'city'              =>  $data[13],
                'state'             =>  $data[14],
                'postal_code'       =>  $data[15],
                'country'           =>  $data[16],
                'description'       =>  $data[17],
                'do_not_call'       =>  $data[18],
                'email_opt_out'     =>  $data[19],
            );
            $mergedData = array_merge($dataToImport, $metaData);

            if ($form->isValid($mergedData)) {
                $strippedDataToInsert = $this->unsetNonTableFields($mergedData);
                $success = $table->insert($strippedDataToInsert);
                ++$noOfAffectedRows;        
            } 
        }
        fclose($handle); 
        if ($success) {
            return $noOfAffectedRows;
        }
        else {
            return $dataToImport['email'];
        }
    }

    /**
     * Get the default lead assignee
     * @param int user id
     */
    public function getDefaultAssigneeId()
    {
        $variable = new Core_Model_Variable(self::VARIABLE_KEY_DEFAULT_ASSIGNEE_ID);
        return $variable->getValue();
    }

    /**
     * Set the default lead assignee
     * @param int $userId the user id
     */
    public function setDefaultAssigneeId($userId)
    {    
        $variable = new Core_Model_Variable();
        $variable->save(self::VARIABLE_KEY_DEFAULT_ASSIGNEE_ID, $userId);

    }

   /**
     * Set all unassigned leads to this user id
     * @param int $userId the user id
     */
    public function assignUnassignedLeadsTo($userId)
    {
        $table = $this->getTable();
        $where = "assigned_to is NULL";
        $data = array(
            'assigned_to' => $userId
        );
        return $table->update($data, $where);
    }

    /**
     * @return object Core_Model_Opportunity_Notes
     */
    public function getNotes()
    {
        $notes = new Core_Model_Lead_Notes();
        $notes->setModel($this);
        return $notes;
    }
 
    /**
     * @param int campaignId
     * @return array the lead record with campaignId
     */
    public function getLeadsByCampaignId($campaignId)
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

    public function getLeads()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);

        $select->from(array('l' => 'lead'),
            array('l'=>'*'))
            ->where('assigned_to = ?', $this->getCurrentUser()->getUserId())
            ->where('converted = 0')
            ->order(array('created DESC'))
            ->limit(5, 0);

        $result = $table->fetchAll($select);
        return $result;

    }
}


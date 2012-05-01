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

class Core_Model_Account extends Core_Model_Abstract
{
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';
    const STATUS_CONVERT = 'CONVERT';

    /**
     * @TODO deprecated
     */
    public $db;
   
    /**
     * @see Core_Model_Abstract::$_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Account';


    /**
     * @var int the account id to operate on
     * @TODO deprecated use $_accountId instead
     */
    protected $_account_id;
    
    /**
     * @var array the default observer classes
     */
    protected $_defaultObservers = array(
        'Core_Model_Account_Notify_Email'
    );

    /**
     * @param int $accountId the account id to operate on
     */
    public function __construct($accountId = null)
    {
        $this->db = Zend_Registry::get('db');
        if (is_numeric($accountId)) {
            $this->_account_id = $accountId;
        }
        parent::__construct($accountId);
    }

    /**
     * @param int $accountId the account id to operate on
     * @TODO deprecated use setAccountId() instead
     */    
    public function setId($accountId = null)
    {
        if (is_numeric($accountId)) {
            $this->_account_id = $accountId;
        }
        return $this;
    }

    /**
     * @param int $accountId
     * @return fluent interface
     */
    public function setAccountId($accountId)
    {
        $this->_account_id = $accountId;
        return $this;
    }

    /**
     * @return int the account ID
     */
    public function getAccountId()
    {
        return $this->_account_id;
    }

    /**
     * Inserts a row in the account table
     * @param array $data
     * @return int the newly created account ID
     */
    public function create($data = array())
    {
        $data = $this->filterInput($data);
		$data['created'] =  time();
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }
        $this->db->insert('account', $data);
        $this->_account_id = $this->db->lastInsertId();
        $this->setStatus(self::STATUS_CREATE);
        
        $log = $this->getLoggerService();
        $info = 'Account created with account id = '. $this->_account_id;
        $log->info($info);
        
        return $this->_account_id;
    }


    /**
     * Fetches a single record in the account table
     * @return result object from Zend_Db_Select
     * based on the accountId
     */
    public function fetch()
    {
        $select = $this->db->select();
        $select->from(array('a'=>'account'), array('*'))
                ->joinLeft(array('u'=>'user'),
                    'u.user_id = a.assigned_to', 
                    array('u.email'=>'email as assignedToEmail'))
                ->joinLeft(array('b'=>'branch'),
                    'a.branch_id = b.branch_id', 
                    array('b.branch_name'=>'branch_name as branch_name'))
                ->where('a.account_id = ?', $this->_account_id);
        $result = $this->db->fetchRow($select);
        return $result;
    }

    /**
     * Similar to above fetch() method 
     * @return array from the Zend_Db_Select object
     */
    public function fetchAsArray()
    {
        $this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $select = $this->db->select();
        $select->from(array('a'=>'account'), array('*'))
                ->where('a.account_id = ?', $this->_account_id);

        $result = $this->db->fetchRow($select);
        $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
        return $result;
    }

    /**
     * Generate the Zend_Db_Select object to use
     */
    public function getfetchAllSelectObject()
    {
        $select = $this->db->select();
        $select->from(array('a'=>'account'), array('*'))
                ->joinLeft(array('u'=>'user'),
                    'a.assigned_to = u.user_id', array('u.email'=>'email as assignedToEmail'))
                ->joinLeft(array('b'=>'branch'),
                    'a.branch_id = b.branch_id', array('b.branch_name'))
        ;

        return $select;
    }
    

    /**
     * Updates a row in the account table
     */
    public function edit($data = array())
    {
        $data = $this->filterInput($data);
        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }

        $accountId = $this->_account_id;
        /*
         * Force the accountId to be integer
         */
        $accountId = $this->db->quote($this->_account_id, 'INTEGER');

        $this->prepareEphemeral();
        $data['updated'] = time();
        $result = $this->db->update('account', $data, "account_id = $accountId");
        $this->setStatus(self::STATUS_EDIT);
        
        $log = $this->getLoggerService();
        $info = 'Account edited with account id = '. $this->_account_id;
        $log->info($info);
        
        return $result;
    }



    /**
     * Deletes a row in the account table
     */
    public function delete()
    {
        $this->prepareEphemeral();
        $where = $this->db->quote($this->_account_id, 'INTEGER');
        $result = $this->db->delete('account', "account_id = $where");
        $this->setStatus(self::STATUS_DELETE);

        $log = $this->getLoggerService();
        $info = 'Account deleted with account id = '. $this->_account_id;
        $log->info($info);
        
        return $result;
    }

    /**
     * Filter the form submitted input to prevent SQL injection attacks
     * And to prevent Zend_Db::insert Exceptions
     * @TODO deprecated
     */
    public function filterInput($data)
    {
        unset($data['submit']);
        return $data;
    }

    /**
     * Initiate index search processing object
     * @return Zend_Db_Select object
     */
    public function getListingSelectObject($search, $sort)
    {
        $process = new Core_Model_Account_Index;
        $processed = $process->getListingSelectObject($search, $sort);
        return $processed;    
    }

    /**
     * Generate Dojo data to assign account to user
     */
    public function getAssignedToDojoData()
    {
        $accountData = new Core_Model_Account_Data;
        $dojoData = $accountData->getAssignedToDojoData();
        return $dojoData;
    }

    /**
     * Generate Dojo data to assign account to branch
     */
    public function getAssignedToBranchDojoData()
    {
        $accountData = new Core_Model_Account_Data;
        $dojoData = $accountData->getAssignedToBranchDojoData();
        return $dojoData;
    }


    /**
     * Get the contacts belonging to this account
     */
    public function getContacts()
    {
        $contacts = new Core_Model_Account_Contacts($this->_account_id);
        return $contacts->getContacts(); 
    }

    /**
     * @return string account name
     */
    public function getName()
    {
       $data = $this->fetch();
       if ($data) {
          return $data->account_name;
       }
       else {
           return;
       }
    }

    public function getNotes()
    {
        $notes = new Core_Model_Account_Notes;
        $notes->setModel($this);
        return $notes;
    }
    
    public function getLedgerId()
    {
       $data = $this->fetch();
       if ($data->ledger_id == "") {
            $financeGroupModel = new Core_Model_Finance_Group;
            $financeGroupId = $financeGroupModel->getGroupIdByName('Sundry Debtors');
            $ledgerDataToInsert['fa_group_id'] = $financeGroupId;
            $ledgerDataToInsert['name'] = $this->getName();
            $ledgerDataToInsert['opening_balance_type'] = "1";
            $ledgerDataToInsert['opening_balance'] = "0";
            $financeLedgerModel = new Core_Model_Finance_Ledger();
            $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
            $dataToUpdate['ledger_id'] = $financeLedgerId;
            $accountId = $this->_account_id;
            $result = $this->db->update('account', $dataToUpdate, "account_id = $accountId");
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
                    'name' => $this->getName(),
                    'fa_group_id' => 
                        $financeGroupModel->getGroupIdByName('Sundry Debtors'),
                    'opening_balance_type' => $data['opening_balance_type'],
                    'opening_balance' => $data['opening_balance'],
                );
         $financeLedgerModel = new Core_Model_Finance_Ledger();
         $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
         
         $dataToUpdate['ledger_id'] = $financeLedgerId;
         $accountId = $this->_account_id;
         $result = $this->db->update('account', $dataToUpdate, "account_id = $accountId");
         
         return $financeLedgerId;
    }
    
    /**
     * @return initialized ledgerId
     */
    public function ledgerExists()
    {
        $accountRecord = $this->fetch();
        if ($accountRecord->ledger_id == ""){
            return 0;
        }
        else {
            return 1;
        }
        
    }

    /**
     * @param int campaignId
     * @return array the accounts record with campaignId
     */
    public function getAccountsByCampaignId($campaignId)
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
     * Import leads account a CSV file
     *
     * @param string $location the location of the CSV file.
     *
     * @return void
     */
    public function import($metaData, $location)
    {
        $form = new Core_Form_Account_Create;
        $table = $this->getTable();
        $noOfAffectedRows = 0;
        $success = null;
        $handle = fopen($location, "r");

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $dataToImport = array(
                'account_name'              =>  $data[0],
                'phone'                     =>  $data[1],
                'mobile'                    =>  $data[2],
                'website'                   =>  $data[3],
                'fax'                       =>  $data[4],
                'email'                     =>  $data[5],
                'billing_address_line_1'    =>  $data[6],
                'billing_address_line_2'    =>  $data[7],
                'billing_address_line_3'    =>  $data[8],
                'billing_address_line_4'    =>  $data[9],
                'billing_city'              =>  $data[10],
                'billing_state'             =>  $data[11],
                'billing_postal_code'       =>  $data[12],
                'billing_country'           =>  $data[13],
                'shipping_address_line_1'   =>  $data[14],
                'shipping_address_line_2'   =>  $data[15],
                'shipping_address_line_3'   =>  $data[16],
                'shipping_address_line_4'   =>  $data[17],
                'shipping_city'             =>  $data[18],
                'shipping_state'            =>  $data[19],
                'shipping_postal_code'      =>  $data[20],
                'shipping_country'          =>  $data[21],
                'description'               =>  $data[22],
                'assigned_to'               =>  $data[23],
                'created_by'                =>  $data[24],
                'created'                   =>  $data[25],
                'branch_id'                 =>  $data[26],
                'updated'                   =>  $data[27],
                'ledger_id'                 =>  $data[28],
                'campaign_id'               =>  $data[29],
                'tin'                       =>  $data[30],
                'pan'                       =>  $data[31],
                'service_tax_number'        =>  $data[32]
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

    public function getAccounts()
    {
        $table = $this->getTable();
        $select = $table->select->setIntegrityCheck(false);
        $select->from(array('a' => 'account'),
            array('a'=>'*'))
            ->where('assigned_to = ?', $this->getCurrentUser()->getUserId())
            ->order(array('created DESC'))
            ->limit(5, 0);

        $result = $table->fetchAll($select);
        return $result;

    }

}

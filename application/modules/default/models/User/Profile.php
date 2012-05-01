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
class Core_Model_User_Profile extends Core_Model_Abstract
{
    
    /**
     * @var int the user id
     */ 
    protected $_user_id;

    /**
     * Table class name
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Profile';



    /**
     * @param $user_id is the user id
     * If the $user_id is not provided default to current user id
     */
    public function __construct($user_id = null)
    {

        if (is_numeric($user_id)) {
            $this->_user_id = $user_id;
        } else {
            $this->_user_id = Core_Model_User_Current::getId();
        }
        parent::__construct();
    }
    
   /**
    * Create a user profile
    *
    * @param array $data
    */
    public function create($data = array())     
    {
        $table = $this->getTable();
        $data = $this->unsetNonTableFields($data);
        return $table->insert($data); 
    }

    /**
     * Set the user id
     * @param int user id
     */
    public function setUserId($userId)
    {
        $this->_user_id = $userId;
    }

    /**
     * @return the user's profile data
     *
     */
    public function fetch()
    {
        $table = $this->getTable();
        $result = $table->findByUserId($this->_user_id);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return string full name of the user
     */
    public function getFullName()
    {
        if (!is_numeric($this->_user_id)) {
            return 'Guest';
        }
        $data = $this->fetch();
        $firstName = $data['first_name'];
        $middleName = $data['middle_name'];
        $lastName = $data['last_name'];
        
        if ($middleName) {
            $fullName = $firstName . ' ' . $middleName . ' ' . $lastName;
        } else {
            $fullName = $firstName . ' ' . $lastName;
        }

        return $fullName; 
    }

    /**
     * @return string reports
     */
    public function getReportsTo()
    {
        $record = $this->fetch();
        return $record['reports_to'];
    }
    
    /**
     * @return int initialized ledgerId
     */
    public function ledgerExists()
    {
        $profileRecord = $this->fetch();
        if ($profileRecord['ledger_id'] == ""){
            return 0;
        }
        else {
            return 1;
        }
        
    }
    
    /**
     * @return int ledgerId
     */
    public function getLedgerId()
    {
       $db = Zend_Registry::get('db');
       $data = $this->fetch();
       if ($data['ledger_id'] == "") {
            $financeGroupModel = new Core_Model_Finance_Group;
            $financeGroupId = $financeGroupModel->getGroupIdByName('Salaries Payable');
            $ledgerDataToInsert['fa_group_id'] = $financeGroupId;
            $ledgerDataToInsert['name'] = $this->getFullName();
            $ledgerDataToInsert['opening_balance_type'] = "1";
            $ledgerDataToInsert['opening_balance'] = "0";
            $financeLedgerModel = new Core_Model_Finance_Ledger();
            $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
            $dataToUpdate['ledger_id'] = $financeLedgerId;
            $result = $db->update('profile', $dataToUpdate, "user_id = $this->_user_id");
            return $financeLedgerId;
       }
       return $data['ledger_id'];
    }
    
    /**
     * @return int advance ledgerId
     */
    public function getAdvanceLedgerId()
    {
       $db = Zend_Registry::get('db');
       $data = $this->fetch();
       if ($data['advance_ledger_id'] == "") {
            $financeGroupModel = new Core_Model_Finance_Group;
            $financeGroupId = $financeGroupModel->getGroupIdByName('Loans And Advances');
            $ledgerDataToInsert['fa_group_id'] = $financeGroupId;
            $ledgerDataToInsert['name'] = $this->getFullName();
            $ledgerDataToInsert['opening_balance_type'] = "1";
            $ledgerDataToInsert['opening_balance'] = "0";
            $financeLedgerModel = new Core_Model_Finance_Ledger();
            $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
            $dataToUpdate['advance_ledger_id'] = $financeLedgerId;
            $result = $db->update('profile', $dataToUpdate, "user_id = $this->_user_id");
            return $financeLedgerId;
       }
       return $data['advance_ledger_id'];
    }
    
    /**
     * @return int initialized ledgerId
     */
    public function initializeLedger($data)
    {
        $db = Zend_Registry::get('db');
        $financeGroupModel = new Core_Model_Finance_Group;
        $ledgerDataToInsert = array (
                    'name' => $this->getFullName(),
                    'fa_group_id' => 
                        $financeGroupModel->getGroupIdByName('Salaries Payable'),
                    'opening_balance_type' => $data['opening_balance_type'],
                    'opening_balance' => $data['opening_balance'],
                );
         $financeLedgerModel = new Core_Model_Finance_Ledger();
         $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
         
         $dataToUpdate['ledger_id'] = $financeLedgerId;
         $result = $db->update('profile', $dataToUpdate, "user_id = $this->_user_id");
         
         return $financeLedgerId;
    }
    
    /**
     * @return int primary role id
     */
    public function getPrimaryRoleId()
    {
        $record = $this->fetch();
        return $record['primary_role'];
    }
}


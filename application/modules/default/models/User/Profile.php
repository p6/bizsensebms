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
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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


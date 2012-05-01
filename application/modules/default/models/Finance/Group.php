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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 */

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Finance_Group extends Core_Model_Abstract
{
    protected $_groupId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_Group';

    /**
     * @param int $group id
     * @return fluent interface
     */
    public function setGroupId($groupId)
    {
        $this->_groupId = $groupId;
        return $this;
    }

    /**
     * @return int the ledger ID
     */
    public function getGroupId()
    {
        return $this->_groupId;
    }
    /**
     * Create a finance group record
     * @param array data
     * @return record id
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $result = $table->insert($data);
        return $result;
    }

    /**
     * Fetches a single group record from db 
     * Based on currently set group Id
     * @return array of group record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('fa_group_id = ?', $this->_groupId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param string $name
     * @return array record
     */
    public function fetchByName($name)
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('name like ?', $name);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
        
    }
    
    /**
     * @param string $categoryId
     * @return array record
     */
    public function fetchByCategoryId($categoryId)
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('fa_group_category_id like ?', $categoryId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
        
    }
    
    /**
     * @param string group name
     * @return int group Id
     */
    public function getGroupIdByName($name)
    {
        $record = $this->fetchByName($name);
        return $record['fa_group_id'];
    }
    
    /**
     * @param int group id
     * @return int group Id
     */
    public function getGroupWiseSummary($groupId)
    {
        if ($groupId) {
            $this->setGroupId($groupId);
            $groupRecord = $this->fetch();
            $groupRecords[] = $groupRecord;
        }
        else {
            $groupRecords = $this->fetchAll();
        }
        $ledgerModel = new Core_Model_Finance_Ledger;
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        
        $result = array();
        
        for($i = 0; $i <= sizeof($groupRecords)-1; $i += 1) {
          $groupName = $groupRecords[$i]['name'];
          $ledgers = $ledgerModel->fetchByGroup($groupRecords[$i]['name']);
          $totalBalance = 0;
          for($x = 0; $x <= sizeof($ledgers)-1; $x += 1) {
            $balance = $ledgerEntryModel->getBalanceByLedgerId(
                                            $ledgers[$x]['fa_ledger_id']);
            $totalBalance += $balance;
          }
          if ($totalBalance > 0) {
           $result[$groupName] = $totalBalance." Cr"; 
          }
          else {
           $result[$groupName] = abs($totalBalance)." Dr"; 
          }
        }
        
        return $result;
    }
}



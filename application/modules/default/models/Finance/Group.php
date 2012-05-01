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



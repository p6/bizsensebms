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

class Core_Model_Finance_Ledger_Entry extends Core_Model_Abstract
{
    protected $_ledgerEntryId;
    protected $_ledgerId;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_Ledger_Entry';
    public function setLedgerId($ledgerId)
    {
        $this->_ledgerId = $ledgerId;
        return $this;
    }

    public function getLedgerId()
    {
        return $this->_ledgerId;
    }
    
    public function setLedgerEntryId($ledgerEntryId)
    {
        $this->_ledgerEntryId = $ledgerEntryId;
        return $this;
    }

    public function getLedgerEntryId()
    {
        return $this->_ledgerEntryId;
    }

    /**
     * Create a finance group record
     * @param array data
     * @return record id
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $this->_ledgerEntryId = $table->insert($data);
        return $this->_ledgerEntryId;
    }
    
    /**
     * delete a row in finance ledger entry record
     * @param Ledger Id
     * @return result
     */
    public function deleteById($ledgerEntryId) 
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto(
            'fa_ledger_entry_id = ?', $ledgerEntryId
        );
        $result = $table->delete($where);
    }
    
    /**
     * delete a row in finance ledger entry record
     * @param Ledger Ids
     * @return result
     */
    public function deleteByIds($ledgerEntryIds) 
    {
        $table = $this->getTable();
        for($i = 0; $i <= sizeof($ledgerEntryIds)-1; $i += 1) {
            $where = $table->getAdapter()->quoteInto(
            'fa_ledger_entry_id = ?', $ledgerEntryIds[$i]
            );
            $result = $table->delete($where);
        }
        return $result;
    }
    
    /**
     * @param Ledger Id
     * @return array of ledger entry record
     */
    public function fetchByLedgerId($ledgerId) 
    {
        $table = $this->getTable();
        
        $financialYear = $this->getFinancialYearWhereClause();
        
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('fa_ledger_id = ?', $ledgerId)
                    ->where($financialYear);
        $result = $table->fetchAll($select);
        
        if ($result) {
            $result = $result->toArray();
        }        
             
        return $result;
    }
    
    /**
     * @return bool
     */
    public function csvExport($data)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false);
        $select->where('fa_ledger_id = ?', $this->_ledgerEntryId);
        
        if ($data['from'] != '' && $data['to'] != '') {
            $fromDate = new Zend_Date($data['from']);
            $from = $fromDate->getTimestamp();
            $toDate = new Zend_Date($data['to']);
            $to = $toDate->getTimestamp();
            $select->where("transaction_timestamp BETWEEN '$from' and '$to'");
        }
        
        if ($data['notes'] != '') {
            $notes = $data['notes'];
            $select->where('notes like ?','%' .$notes .'%' );
        }
        
        $result = $table->fetchAll($select);
        
        if ($result) {
            $result = $result->toArray();
        }  
               
        $ledgerModel = new Core_Model_Finance_Ledger($this->_ledgerEntryId);
        $ledgerRecord = $ledgerModel->fetch();
                       
        $file = "Ledger Name ,".$ledgerRecord['name']; 
        $file .= PHP_EOL;
        $file .= "Category,".$ledgerRecord['fa_group_category_name'];
        $file .= PHP_EOL;
       
        $file .= "Transaction Id".","."Debit".","."Credit".","."Balance".","
                ."Notes".","."Transaction Time";
       
        $balance = 0;
        for($i = 0; $i <= sizeof($result)-1; $i += 1) {
            $file .= PHP_EOL;
            $file .= $result[$i]['fa_ledger_entry_id'].',';
            $file .= $result[$i]['debit'].',';
            $file .= $result[$i]['credit'].',';
            $currentBalance = $result[$i]['debit'] - $result[$i]['credit'];
            $balance = $balance - $currentBalance;
            if ($balance > 0) {
                $file .= $balance." Cr".','; 
            }
            else {
                $file .= abs($balance)." Dr".','; 
            }
            $file .= $result[$i]['notes'].',';
            $date = new Zend_Date();
            $date->setTimestamp($result[$i]['transaction_timestamp']);
            $file .= $date->toString().',';
        }
        
       return $file;
    }
       
    /**
     * @param Ledger Id
     * @return decimal Ledger Balance
     */
    public function getBalanceByLedgerId($ledgerId)
    {
        $ledgerReceord = $this->fetchByLedgerId($ledgerId);
        $ledgerModel = new Core_Model_Finance_Ledger($ledgerId);
        $balanceType = $ledgerModel->getLedgerBalanceTypeById();
        
        $balance = 0;
        for($i = 0; $i <= sizeof($ledgerReceord)-1; $i += 1) {
           $currentBalance = $ledgerReceord[$i]['debit'] - $ledgerReceord[$i]['credit'];
           if ($balanceType == Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_CREDIT) {
               $balance = $balance - $currentBalance;
           }
                
           if ($balanceType == Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_DEBIT) {
                $balance = $balance + $currentBalance;
           }
                
           if ($balanceType == Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_DEBITCREDIT) {
               if ($balance > 0) {
                   $balance = $balance - $currentBalance;
               }
               else {
                   $balance = $balance + $currentBalance;
                    }
                }
        }
        return $balance; 
    }
    
    /**
     * @param Ledger Id nad $date
     * @return array of data
     */
    public function fetchEntriesByDate($date, $ledgerId)
    {
        $date = new Zend_Date($date);
        $startDate = $date->getTimestamp();
                
        $endDate = $date->addDay(1);
        $endDate = $endDate->getTimestamp();
        
        $financialYear = $this->getFinancialYearWhereClause();
        
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where("transaction_timestamp BETWEEN '$startDate' and '$endDate'")
                    ->where('fa_ledger_id = ?', $ledgerId)
                    ->where($financialYear);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param Ledger Id nad $date
     * @return array of data
     */
    public function fetchEntriesForClosing($date, $ledgerId)
    {
        $date = new Zend_Date($date);
        $date = $date->getTimestamp();
        
        $financialYear = $this->getFinancialYearWhereClause();
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where("transaction_timestamp >= '$date'")
                    ->where('fa_ledger_id = ?', $ledgerId)
                    ->where($financialYear);
                    
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return array the ledger entry record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('fa_ledger_entry_id = ?', $this->_ledgerEntryId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return bool
     */
    public function edit($data)
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('fa_ledger_entry_id = ?', $this->_ledgerEntryId);
        $result = $table->update($data, $where);
        return $result;
    }
    
    /**
     * @return array 
     */
    public function openingBalanceSummary()
    {
        $ledgerModel = new Core_Model_Finance_Ledger();
        $ledgerRecords = $ledgerModel->fetchAll();
        $totalDebit = 0;
        $totalCredit = 0;
        for($i = 0; $i <= sizeof($ledgerRecords)-1; $i += 1) {
            $this->setLedgerEntryId(
                        $ledgerRecords[$i]['opening_balance_ledger_entry_id']); 
            $ledgerEntryRecord = $this->fetch();
            
            $totalDebit +=  $ledgerEntryRecord['debit'];   
            $totalCredit +=  $ledgerEntryRecord['credit'];
        }
        $total['debit'] = $totalDebit;
        $total['credit'] = $totalCredit;
        return $total;
    }
    
    /**
     * @return string where clause 
     */
    public function getFinancialYearWhereClause()
    {
        $variableModel = new Core_Model_Variable('finance_year_start_date');
        $startDateElement = $variableModel->getValue();
                
        $variableModel = new Core_Model_Variable('finance_year_end_date');
        $endDateElement = $variableModel->getValue();
        
        if ($startDateElement != '' && $endDateElement != '') {
            $startDate = new Zend_Date($startDateElement);
            $startDate = $startDate->getTimestamp();
            
            $endDate = new Zend_Date($endDateElement);
            $endDate = $endDate->getTimestamp();
            $whereClause = 
                   "transaction_timestamp BETWEEN '$startDate' and '$endDate'";
        } else {
             $whereClause = "transaction_timestamp BETWEEN '' and '' ";
        }
      
        return $whereClause;
    }
}

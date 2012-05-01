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
class Core_Model_Finance_BankAccount_Transaction extends Core_Model_Abstract
{
    /**
     * @var the bank Transaction ID
     */
     protected $_bankTransactionId;
    
    /**
     * Transaction type
     */
     const TRANSACTION_TYPE_DEPOSIT = 1;
     const TRANSACTION_TYPE_WITHDRAW = 2;
     
    /**
     * @param bankTransactionId
     */
     public function __construct($bankTransactionId = null)
     {
        if (is_numeric($bankTransactionId)) {  
            $this->_bankTransactionId = $bankTransactionId;
        }
        parent::__construct();
     }
     
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_BankTransaction';
     
    /**
     * @param int bankTransactionId
     * @return fluent interface
     */
    public function setBankTransactionId($bankTransactionId)
    {
        $this->_bankTransactionId = $bankTransactionId;
        return $this;
    }

    /**
     * @return int the bank Transaction ID
     */
    public function getBankTransactionId()
    {
        return $this->_bankTransactionId;
    }


    /**
     * Create a finance Bank Transaction
     * @param array $data with keys
     * @return int Bank Transaction ID 
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $this->_bankTransactionId = $table->insert($data);
        
        $log = $this->getLoggerService();
        $info = 'Bank Transaction created with transaction id = '.  
                                             $this->_bankTransactionId;
        $log->info($info);
        
        return $this->_bankTransactionId;
    }
    
    /**
     * Fetches a single Bank Transaction record from db 
     * Based on currently set bankTransactionId
     * @return array of Bank Transaction record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                  ->setIntegrityCheck(false)
                  ->where('bank_Transaction_id = ?', $this->_bankTransactionId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param bank account id
     * @return int total amount
     */
    public function getBalanceByBankAccountId($bankAccountId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('bank_account_id = ?', $bankAccountId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        $totalAmount = 0;
        for ($i = 0; $i < count($result); $i++ ) {
            if ($result[$i]['transaction_type'] == self::TRANSACTION_TYPE_DEPOSIT) {
                $totalAmount = $totalAmount + $result[$i]['amount'];
            }
            else {
                $totalAmount = $totalAmount - $result[$i]['amount'];
            }
        }
        return $totalAmount;
    }
    
    /**
     * @param array $data with keys
     * withdraw cash amount into appropriate ledger
     * @return fluent interface
     */
    public function editTransaction($data)
    {
        $transactionRecord = $this->fetch();
        $ledgerEntryIds = unserialize($transactionRecord['s_ledger_entry_ids']);
        
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryModel->deleteByIds($ledgerEntryIds);
        
        $bankAccountModel = new Core_Model_Finance_BankAccount(
                                       $transactionRecord['bank_account_id']);
        $bankAccountRecord = $bankAccountModel->fetch();  
        $cashaccountModel = 
                    new Core_Model_Finance_CashAccount($data['cashaccount_id']);
        $ledgerId = $cashaccountModel->getLedgerId();
        $notes = "Deposit cash from bank account number - ".
                  $bankAccountRecord['account_no'].' and bank name - '.
                  $bankAccountRecord['bank_name'];
        $bankModel = new Core_Model_Finance_BankAccount($transactionRecord['bank_account_id']);
        
        $fa_ledger_entry_ids = array (
          '0' => $this->ledgerEntries('0',$data['amount'],$notes,$ledgerId),
          '1' => $this->ledgerEntries($data['amount'],'0','Deposit Cash',
                                                          $bankModel->getLedgerId())
        );
        $data['s_ledger_entry_ids'] = serialize($fa_ledger_entry_ids);
                                                 
        $table = $this->getTable();
        $dataToUpdate = array(
            'amount' => $data['amount'],
            'cash_account_id' => $data['cashaccount_id'],
            's_ledger_entry_ids' => $data['s_ledger_entry_ids']
        );
        $where = $table->getAdapter()->quoteInto('bank_transaction_id  = ?', 
                                                   $this->_bankTransactionId);
        $result = $table->update($dataToUpdate, $where);
        
        $log = $this->getLoggerService();
        $info = 'Bank Transaction edited with bank transaction id = '.  
                                             $this->_bankTransactionId;
        $log->info($info);
    }
    
    /**
     * @param debit
     * @param credit
     * @param notes
     * @param ledgerId
     * make ledger entries
     * @TODO deprecated
     * @return ledger entry id
     */
    public function ledgerEntries($debit, $credit, $notes, $ledgerId)
    {
        $dataToInsert = array(
             'debit' => $debit,
             'credit' =>  $credit,
             'notes' => $notes,
             'transaction_timestamp' => time(),
             'fa_ledger_id' => $ledgerId
           );
       $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
       $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
       
       return $ledgerEntryId;
    }
    
    /**
     * deletes a row in table based on currently set bankTransctionId
     * deletes respective ledger entry details
     * @return int
     */
    public function delete()
    {
        $bankTransactionRecord = $this->fetch();
        $ledgerEntryIds = unserialize(
                                $bankTransactionRecord['s_ledger_entry_ids']);
        $financeLedgerModel = new Core_Model_Finance_Ledger_Entry();
        $result = $financeLedgerModel->deleteByIds($ledgerEntryIds);
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('bank_transaction_id  = ?',
                                                     $this->_bankTransactionId);
        $result = $table->delete($where);
        $log = $this->getLoggerService();
        $info = 'Bank Transaction deleted with bank transaction id = '.  
                                             $this->_bankTransactionId;
        $log->info($info);
        return $result;
    }
}



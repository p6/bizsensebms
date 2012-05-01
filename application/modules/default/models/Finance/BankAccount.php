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

class Core_Model_Finance_BankAccount extends Core_Model_Abstract
{
    /**
     * @var the bank account ID
     */
     protected $_bankAccountId;
    
    /**
     * @var the ledger entry model
     */
    protected $_ledgerEntryModel;
    
    /**
     * @var transaction date and time
     */
    protected $_transactionDateTime;

    /**
     * @param bankAccountId
     */
     public function __construct($bankAccountId = null)
     {
        if (is_numeric($bankAccountId)) {  
            $this->_bankAccountId = $bankAccountId;
        }
        parent::__construct();
     }
     
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_BankAccount';
     
    /**
     * @param int bankAccountId
     * @return fluent interface
     */
    public function setBankAccountId($bankAccountId)
    {
        $this->_bankAccountId = $bankAccountId;
        return $this;
    }

    /**
     * @return int the bank account ID
     */
    public function getBankAccountId()
    {
        return $this->_bankAccountId;
    }


    /**
     * Create a finance Bank Account
     * @param array $data with keys
     * @return int Bank Account ID 
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $dataToInsert = array(
            'account_no' => $data['account_no'],
            'bank_name' => $data['bank_name'],
            'bank_branch' => $data['bank_branch'],
        );
        $financeGroupModel = new Core_Model_Finance_Group;
        $ledgerDataToInsert = array (
                    'name' => $data['bank_name']."-".$data['account_no'],
                    'fa_group_id' => 
                        $financeGroupModel->getGroupIdByName('Bank Accounts'),
                    'opening_balance_type' => 
                        $data['opening_balance_type'],
                    'opening_balance' => $data['opening_balance'],
                );
        
        $financeLedgerModel = new Core_Model_Finance_Ledger();
        $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
        
        $dataToInsert['fa_ledger_id'] = $financeLedgerId;
        $this->_bankAccountId = $table->insert($dataToInsert);
        
       
        $log = $this->getLoggerService();
        $info = 'Bank account created with bank account id = '.
                                                        $this->_bankAccountId;
        $log->info($info);
        return $this->_bankAccountId;
    }
    
    /**
     * @return Ledger Id
     */
    public function getLedgerId()
    {
       $data = $this->fetch();
       return $data['fa_ledger_id'];
    }
    
    /**
     * @return string Bank name
     */
    public function getBankName()
    {
       $data = $this->fetch();
       return $data['bank_name'];
    }
    
    /**
     * @return number of rows
     */
    public function checkAccountNumberAndName($accountNumber, $bankName)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('account_no = ?', $accountNumber)
                    ->where('bank_name = ?', $bankName);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        
        $rowCount = count($result);
       return $rowCount;
    }
    
    /**
     * Fetches a single Bank account record from db 
     * Based on currently set bankAccountId
     * @return array of Bank account record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('bank_account_id = ?', $this->_bankAccountId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param array $data with keys
     * updates bankaccount details and ledger entries
     * @return int
     */
    public function edit($data)
    {
        $table = $this->getTable();
        $dataToUpdate = array(
            'account_no' => $data['account_no'],
            'bank_name' => $data['bank_name'],
            'bank_branch' => $data['bank_branch'],
        );
        $where = $table->getAdapter()->quoteInto('bank_account_id  = ?', 
                                                         $this->_bankAccountId);
        $result = $table->update($dataToUpdate, $where);
        
        $bankAccountRecord = $this->fetch();
        $ledgerId = $bankAccountRecord['fa_ledger_id'];
        $ledgerData['name'] = $data['bank_name']."-".$data['account_no'];
        $financeLedgerModel = new Core_Model_Finance_Ledger($ledgerId);
        $financeLedgerModel->edit($ledgerData);
        
        $log = $this->getLoggerService();
        $info = 'Bank account edited with bank account id = '.
                                                        $this->_bankAccountId;
        $log->info($info);
        return $result;
    }
    
    /**
     * deletes a row in table based on currently set bankAccountId
     * deletes respective ledger entry details
     * @return int
     */
    public function delete()
    {
        $bankaccountRecord = $this->fetch();
        $ledgerId = $bankaccountRecord['fa_ledger_id'];
        $financeLedgerModel = new Core_Model_Finance_Ledger($ledgerId);
        $financeLedgerModel->delete();
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('bank_account_id = ?', 
                                                         $this->_bankAccountId);
        $result = $table->delete($where);
        
        $log = $this->getLoggerService();
        $info = 'Bank account deleted with bank account id = '.
                                                        $this->_bankAccountId;
        $log->info($info);
        return $result;
    }
    
    /**
     * @return int bank account number
     */
    public function getAccountNumber()
    {
        $bankaccountRecord = $this->fetch();
        return $bankaccountRecord['account_no'];
    }
    
    /**
     * @param array $data with keys
     * deposite cash amount into appropriate ledger
     */
    public function depositcash($data)
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDateTime = $date->getTimestamp();
        
        $bankAccountRecord = $this->fetch();  
        $cashaccountModel = 
                    new Core_Model_Finance_CashAccount($data['cashaccount_id']);
        $ledgerId = $cashaccountModel->getLedgerId();
        $notes = "Deposit cash from bank account number - ".
                  $bankAccountRecord['account_no'].' and bank name - '.
                  $bankAccountRecord['bank_name'];
                  
         $fa_ledger_entry_ids = array (
          '0' => $this->ledgerEntries('0',$data['amount'],$notes,$ledgerId),
          '1' => $this->ledgerEntries($data['amount'],'0',$notes ,
                                                          $this->getLedgerId())
        );
        $data['s_ledger_entry_ids'] = serialize($fa_ledger_entry_ids);
        $data = array (
            'bank_account_id' => $this->_bankAccountId,
            'transaction_type' => 
          Core_Model_Finance_BankAccount_Transaction::TRANSACTION_TYPE_DEPOSIT,
            'amount' => $data['amount'],
            'cash_account_id' => $data['cashaccount_id'],
            's_ledger_entry_ids' => $data['s_ledger_entry_ids'],
            'date' => $date->getTimestamp()
        );
        $bankTransaction = new Core_Model_Finance_BankAccount_Transaction;
        $result = $bankTransaction->create($data);
    }   
    
    /**
     * @param array $data with keys
     * withdraw cash amount into appropriate ledger
     * @return fluent interface
     */
    public function withdrawcash($data)
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDateTime = $date->getTimestamp();
        
        $cashaccountModel = 
                    new Core_Model_Finance_CashAccount($data['cashaccount_id']);
        $ledgerId = $cashaccountModel->getLedgerId();
        $bankAccountRecord = $this->fetch();       
        $notes = "Withdraw cash from bank account number - ".
                  $bankAccountRecord['account_no'].' and bank name - '.
                  $bankAccountRecord['bank_name'];
        
        $fa_ledger_entry_ids = array (
          '0' => $this->ledgerEntries($data['amount'],'0', $notes, $ledgerId),
          '1' => $this->ledgerEntries('0', $data['amount'], $notes ,
                                                          $this->getLedgerId())
        );
        $data['s_ledger_entry_ids'] = serialize($fa_ledger_entry_ids);
        
        $data = array (
            'bank_account_id' => $this->_bankAccountId,
            'transaction_type' => 
          Core_Model_Finance_BankAccount_Transaction::TRANSACTION_TYPE_WITHDRAW,
            'amount' => $data['amount'],
            'cash_account_id' => $data['cashaccount_id'],
            's_ledger_entry_ids' => $data['s_ledger_entry_ids'],
            'date' => $date->getTimestamp()
        );
        $bankTransaction = new Core_Model_Finance_BankAccount_Transaction;
        $result = $bankTransaction->create($data);
    }   
    
    /**
     * withdraw cash amount into appropriate ledger
     * @param array $data with keys, 
     * @param int receiptBankId  
     * @param int paymentBankId
     * @return fluent interface
     */
    public function reconciliationConfirm($data, $receiptBankId, $paymentBankId)
    {
        if ($receiptBankId != '') {
            $receiptBankModel = new Core_Model_Finance_Receipt_Bank(
                                                            $receiptBankId);
            $date = new Zend_Date($data['reconciliation_date']);
            $dataToUpdate['reconciliation_date'] = $date->getTimestamp();
            $receiptBankModel->update($dataToUpdate);
        }
        
        if ($paymentBankId != '') {
            $paymentBankModel = new Core_Model_Finance_Payment_Bank(
                                                            $paymentBankId);
            $date = new Zend_Date($data['reconciliation_date']);
            $dataToUpdate['reconciliation_date'] = $date->getTimestamp();
            $paymentBankModel->update($dataToUpdate);
        }
        return $this;
    }   
    
    /**
     * @param array $data with keys
     * withdraw cash amount into appropriate ledger
     * @return fluent interface
     */
    public function returned($data, $bankaccountId, $receiptBankId, 
                                                             $paymentBankId)
    {
        if ($receiptBankId != '') {
            $receiptBankModel = new Core_Model_Finance_Receipt_Bank(
                                                             $receiptBankId);
            $receiptBankRecord = $receiptBankModel->fetch();
           
            $receiptModel = new Core_Model_Finance_Receipt(
                                            $receiptBankRecord['receipt_id']);
            $receiptRecord = $receiptModel->fetch();
            
            $dataToInsert = array(
                'debit' => "0",
                'credit' => $receiptRecord['amount'],
                'notes' => 'Returned Check',
                'transaction_timestamp' => time(),
                'fa_ledger_id' => $this->getLedgerId()
            );
            
            $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
            $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
            
            $date = new Zend_Date($data['reconciliation_date']);
            $dataToUpdate['reconciliation_date'] = $date->getTimestamp();
            $dataToUpdate['returned'] = '1';
            $receiptBankModel->update($dataToUpdate);
        }
        
        if ($paymentBankId != '') {
            $paymentBankModel = new Core_Model_Finance_Payment_Bank(
                                                            $paymentBankId);
            $paymentBankRecord = $paymentBankModel->fetch();
            $paymentModel = new Core_Model_Finance_Payment(
                                            $paymentBankRecord['payment_id']);
            $paymentRecord = $paymentModel->fetch();
                       
            $dataToInsert = array(
            'debit' => $paymentRecord['amount'],
            'credit' => '0',
            'notes' => 'Returned Check',
            'transaction_timestamp' => time(),
            'fa_ledger_id' => $this->getLedgerId()
            );
            
            $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
            $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
            
            $date = new Zend_Date($data['reconciliation_date']);
            $dataToUpdate['reconciliation_date'] = $date->getTimestamp();
            $dataToUpdate['returned'] = '1';
            $paymentBankModel->update($dataToUpdate);
        }
        
        return $this;
    }     
    /**
     * @return object Core_Model_Finance_Ledger_Entry 
     */
    public function getLedgerEntryModel()
    {
        if (null === $this->_ledgerEntryModel) {
            $this->_ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        }
        return $this->_ledgerEntryModel;
    }

    /**
     * @param debit
     * @param credit
     * @param notes
     * @param ledgerId
     * make ledger entries
     * @TODO deprecated
     * @return fluent interface
     */
    public function ledgerEntries($debit, $credit, $notes, $ledgerId)
    {
        $dataToInsert = array(
             'debit' => $debit,
             'credit' =>  $credit,
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionDateTime,
             'fa_ledger_id' => $ledgerId
           );
       $ledgerEntryId = $this->getLedgerEntryModel()->create($dataToInsert);
    }

    /**
     * @return string PDF file location 
     */
    public function fetchBankTransactionsByDate($date)
    {
        $bankRecords = $this->fetchAll();
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        for($i = 0; $i <= sizeof($bankRecords)-1; $i += 1) {
            $ledgerId = $bankRecords[$i]['fa_ledger_id'];
            $result[] = $ledgerEntryModel->fetchEntriesByDate($date, $ledgerId);
        }
        return $result;
    }
}



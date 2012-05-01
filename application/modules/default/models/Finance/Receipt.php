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
class Core_Model_Finance_Receipt extends Core_Model_Abstract
{
    /**
     * @var the receipt ID
     */
    protected $_receiptId;
    
    const FROM_TYPE = 'receipt from type';
    const FROM_TYPE_ACCOUNT = 1;
    const FROM_TYPE_CONTACT = 2;   

    const RECEIPT_TO_SUNDRY_DEBTORS_ACCOUNT = 1;
    const RECEIPT_TO_SUNDRY_DEBTORS_CONTACT = 2;
    const RECEIPT_TOWARDS_INDIRECT_INCOME = 3;  
      
    const CASH = 0;
    const DD_CHECK = 1;   
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_Receipt';

    /**
     * @var to store date for transaction
     */
    protected $_transactionTime;
    
    /**
     * @param receiptId
     */
    public function __construct($receiptId = null)
     {
        if (is_numeric($receiptId)) {  
            $this->_receiptId = $receiptId;
        }
        parent::__construct();
     }
    /**
     * @param int $receiptId
     * @return fluent interface
     */
    public function setReceiptId($receiptId)
    {
        $this->_receiptId = $receiptId;
        return $this;
    }

    /**
     * @return int the $receiptId
     */
    public function getReceiptId()
    {
        return $this->_receiptId;
    }


    /**
     * Create a finance Sundry Debtors Cheque Receipt
     * @param array $data with keys 
     * @return int receipt ID 
     */
    public function createChequeReceipt($data = array())
    {
        $table = $this->getTable();
        if ($data['from_type'] == 1) {
            $fromType = self::RECEIPT_TO_SUNDRY_DEBTORS_ACCOUNT;
            $accountModel =  new Core_Model_Account($data['account_id']);
            $data['type_id'] = $data['account_id'];
            $from = $accountModel->getName();
            $ledgerId = $accountModel->getLedgerId();
        } else {
            $fromType = self::RECEIPT_TO_SUNDRY_DEBTORS_CONTACT;
            $contactModel =  new Core_Model_Contact($data['contact_id']);
            $data['type_id'] = $data['contact_id'];
            $from = $$contactModel->getFullName();
            $ledgerId = $contactModel->getLedgerId();
        }
        
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $notes = 'Receipt : mode - cheque from - '.$from;
        $ledgerEntryId['0'] = $this->bankLegerEntry($data['bank_account_id'],
                                             $data['amount'], $notes);
        $ledgerEntryId['1'] = $this->customerLegerEntry($ledgerId,
                                                    $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryId);
        
        $dataToInsert = array(
            'mode' => self::DD_CHECK,
            'type' => $fromType,
            'type_id' => $data['type_id'],
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'mode_account_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds
        );
        
        $this->_receiptId = parent::create($dataToInsert); 
        
        $dataToInsertReceiptBank = array(
             'receipt_id' => $this->_receiptId,
             'bank_name' => $data['bank_name'],
             'bank_branch' => $data['bank_branch'],
             'instrument_number' =>$data['instrument_number'],
             'instrument_account_no' => $data['instrument_account_no'],
             'instrument_date' => $date->getTimestamp(),
          );
        $receiptBankModel =  new Core_Model_Finance_Receipt_Bank;
        $result = $receiptBankModel->create($dataToInsertReceiptBank);
        
        $log = $this->getLoggerService();
        $info = 'Cheque Receipt created with receipt id = '. $this->_receiptId;
        $log->info($info);
        
        return $this->_receiptId;
    }

    /**
     * @param bankAccountId
     * @param amount
     * @return int Bank Ledger Entry Id
     */
    protected function bankLegerEntry($bank_account_id, $amount, $notes) 
    {
        $bankAccountModel = new Core_Model_Finance_BankAccount($bank_account_id);
        $ledgerId = $bankAccountModel->getLedgerId();
        $dataToInsert['debit'] = $amount;
        $dataToInsert['credit']= "0";
        $dataToInsert['notes'] = $notes;
        $dataToInsert['transaction_timestamp'] = $this->_transactionTime;
        $dataToInsert['fa_ledger_id'] = $ledgerId;
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledger Id and amount
     * @return int Ledger Entry Id
     */
    protected function customerLegerEntry($ledgerId, $amount, $notes) 
    {
        $dataToInsert['debit'] = "0";
        $dataToInsert['credit']= $amount;
        $dataToInsert['notes'] = $notes;
        $dataToInsert['transaction_timestamp'] = $this->_transactionTime;
        $dataToInsert['fa_ledger_id'] = $ledgerId;
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * Create a finance Sundry Debtors Cash Receipt
     * @param array $data with keys 
     * @return int receipt ID 
     */
    public function createCashReceipt($data = array())
    {
        if ($data['from_type'] == 1) {
            $fromType = self::RECEIPT_TO_SUNDRY_DEBTORS_ACCOUNT;
            $accountModel =  new Core_Model_Account($data['account_id']);
            $data['type_id'] = $data['account_id'];
            $from = $accountModel->getName();
            $ledgerId = $accountModel->getLedgerId();
        } else {
            $fromType = self::RECEIPT_TO_SUNDRY_DEBTORS_CONTACT;
            $contactModel =  new Core_Model_Contact($data['contact_id']);
            $data['type_id'] = $data['contact_id'];
            $from = $$contactModel->getFullName();
            $ledgerId = $contactModel->getLedgerId();
        }
        
        $notes = 'Receipt : mode - cash from - '.$from;
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $ledgerEntryIds = array(
             '0' => $this->customerLegerEntry($ledgerId, 
                                                  $data['amount'], $notes),
             '1' => $this->cashLegerEntry($data['cashaccount_id'],
                                          $data['amount'], $notes)
        );
        
        $fa_ledger_entry_ids = serialize($ledgerEntryIds);
        $dataToInsert = array(
            'mode' => self::CASH,
            'type' => $fromType,
            'type_id' => $data['type_id'],
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'mode_account_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $fa_ledger_entry_ids
        );
               
        $this->_receiptId = parent::create($dataToInsert); 
        
        $log = $this->getLoggerService();
        $info = 'Cash Receipt created with receipt id = '. $this->_receiptId;
        $log->info($info);
        
        return $this->_receiptId;
    }
    
    /**
     * @param cash account Id and amount
     * @return cash Ledger Entry Id
     */
    protected function cashLegerEntry($cashaccountId, $amount, $notes) 
    {
        $cashaccountModel = new Core_Model_Finance_CashAccount($cashaccountId);
        $ledgerId = $cashaccountModel->getLedgerId();
        $dataToInsert['debit'] = $amount;
        $dataToInsert['credit']= "0";
        $dataToInsert['notes'] = $notes;
        $dataToInsert['transaction_timestamp'] = $this->_transactionTime;
        $dataToInsert['fa_ledger_id'] = $ledgerId;
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledger Id and amount
     * @return income Ledger Entry Id
     */
    protected function incomeLegerEntry($ledgerId, $amount, $notes) 
    {
        $dataToInsert = array(
            'debit' => "0",
            'credit' => $amount,
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionTime,
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * Fetches a single Receipt record from db 
     * Based on currently set receiptId
     * @return array of Receipt record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('receipt_id = ?', $this->_receiptId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    
    /**
     * @param array $data
     * @return int
     */
    public function edit($data = array())
    {   
        if ($data['from_type'] == 1) {
            $fromType = self::RECEIPT_TO_SUNDRY_DEBTORS_ACCOUNT;
            $accountModel =  new Core_Model_Account($data['account_id']);
            $data['type_id'] = $data['account_id'];
            $from = $accountModel->getName();
            $ledgerId = $accountModel->getLedgerId();
        } else {
            $fromType = self::RECEIPT_TO_SUNDRY_DEBTORS_CONTACT;
            $contactModel =  new Core_Model_Contact($data['contact_id']);
            $data['type_id'] = $data['contact_id'];
            $from = $$contactModel->getFullName();
            $ledgerId = $contactModel->getLedgerId();
        }
        
        $notes = 'Receipt : mode - cheque from - '.$from;
        $receiptRecord = $this->fetch();
        $lederEntryIdToDelete = 
                           unserialize($receiptRecord['s_fa_ledger_entry_ids']);
       
        $LedgerEntyModel = new Core_Model_Finance_Ledger_Entry;
        $result = $LedgerEntyModel->deleteByIds($lederEntryIdToDelete);
        
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $ledgerEntryId['0'] = $this->bankLegerEntry($data['bank_account_id'],
                                                   $data['amount'], $notes);
        $ledgerEntryId['1'] = $this->customerLegerEntry($ledgerId,
                                                   $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryId);
        
        $dataToInsert = array(
            'mode' => self::DD_CHECK,
            'type' => $fromType,
            'type_id' => $data['type_id'],
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'branch_id' => $data['branch_id'],
            'mode_account_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds
        );
        
        $table = $this->getTable();
        
        $where = $table->getAdapter()
                    ->quoteInto('receipt_id = ?', $this->_receiptId);
        
        $result = $table->update($dataToInsert, $where);
        
        $dataToInsertReceiptBank = array(
             'receipt_id' => $this->_receiptId,
             'bank_name' => $data['bank_name'],
             'bank_branch' => $data['bank_branch'],
             'instrument_account_no' => $data['instrument_account_no'],
             'instrument_number' => $data['instrument_number'],
             'instrument_date' => $date->getTimestamp(),
              );
              
        $receiptBankModel =  new Core_Model_Finance_Receipt_Bank;
        $result = $receiptBankModel->edit($dataToInsertReceiptBank,
                                                             $this->_receiptId);
        $log = $this->getLoggerService();
        $info = 'Cheque Receipt edited with receipt id = '. $this->_receiptId;
        $log->info($info);
        return $result;
    }
    
    /**
     * @param array $data
     * @return int
     */
    public function editCashReceipt($data = array())
    {   
        if ($data['from_type'] == 1) {
            $fromType = self::RECEIPT_TO_SUNDRY_DEBTORS_ACCOUNT;
            $accountModel =  new Core_Model_Account($data['account_id']);
            $data['type_id'] = $data['account_id'];
            $from = $accountModel->getName();
            $ledgerId = $accountModel->getLedgerId();
        } else {
            $fromType = self::RECEIPT_TO_SUNDRY_DEBTORS_CONTACT;
            $contactModel =  new Core_Model_Contact($data['contact_id']);
            $data['type_id'] = $data['contact_id'];
            $from = $$contactModel->getFullName();
            $ledgerId = $contactModel->getLedgerId();
        }
        
        $notes = 'Receipt : mode - cash from - '.$from;
               
        $receiptRecord = $this->fetch();
        $lederEntryIdToDelete = 
                           unserialize($receiptRecord['s_fa_ledger_entry_ids']);
        $LedgerEntyModel = new Core_Model_Finance_Ledger_Entry;
        for($i = 0; $i <= sizeof($lederEntryIdToDelete)-1; $i += 1) {
            $result = $LedgerEntyModel->deleteById($lederEntryIdToDelete[$i]);
        }
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $ledgerEntryIds = array();
        $ledgerEntryIds['0'] = $this->customerLegerEntry($ledgerId,
                                                      $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->cashLegerEntry($data['cashaccount_id'],
                                                      $data['amount'], $notes);
        $fa_ledger_entry_ids = serialize($ledgerEntryIds);
        
        $dataToUpdate = array(
            'mode' => self::CASH,
            'type' => $fromType,
            'type_id' => $data['type_id'],
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'branch_id' => $data['branch_id'],
            'mode_account_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $fa_ledger_entry_ids
        );
        
        $table = $this->getTable();
        
        $where = $table->getAdapter()
                    ->quoteInto('receipt_id = ?', $this->_receiptId);
        
        $result = $table->update($dataToUpdate, $where);
        
        $dataToInsertReceiptBank = array(
             'receipt_id' => $this->_receiptId,
             'bank_name' => $data['bank_name'],
             'bank_branch' => $data['bank_branch'],
             'instrument_account_no' => $data['instrument_account_no'],
             'instrument_date' => $date->getTimestamp(),
              );
              
        $receiptBankModel =  new Core_Model_Finance_Receipt_Bank;
        $result = $receiptBankModel->edit($dataToInsertReceiptBank,
                                                             $this->_receiptId);
        $log = $this->getLoggerService();
        $info = 'Cash Receipt edited with receipt id = '. $this->_receiptId;
        $log->info($info);
        return $result;
    }
    

    /**
     * deletes a row in table based on currently set receiptId
     * deletes respective ledger entry details
     * @return int
     */
    public function delete()
    {
        $receiptRecord = $this->fetch();
        $lederEntryIdToDelete = 
                           unserialize($receiptRecord['s_fa_ledger_entry_ids']);
        $LedgerEntyModel = new Core_Model_Finance_Ledger_Entry;
        for($i = 0; $i <= sizeof($lederEntryIdToDelete)-1; $i += 1) {
            $result = $LedgerEntyModel->deleteById($lederEntryIdToDelete[$i]);
        }
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('receipt_id = ?', $this->_receiptId);
        $log = $this->getLoggerService();
        $info = 'Receipt deleted with receipt id = '. $this->_receiptId;
        $log->info($info);
        return $table->delete($where);
    }
    
    /**
     * Create a finance  Indirect Income Cash Receipt
     * @param array $data with keys 
     * @return int receipt ID 
     */
    public function createIndirectIncomeCashReceipt($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $notes = 'Receipt : mode - cash Type - Indirect Income Cash Receipt';
        
        $ledgerEntryIds = array(
            '0' => $this->incomeLegerEntry($data['ledger_id'], 
                                           $data['amount'], $notes),
            '1' => $this->cashLegerEntry($data['cashaccount_id'],
                                         $data['amount'], $notes)
        );
        
        $fa_ledger_entry_ids = serialize($ledgerEntryIds);
        $dataToInsert = array(
            'mode' => self::CASH,
            'type' => self::RECEIPT_TOWARDS_INDIRECT_INCOME,
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'mode_account_id' => $data['cashaccount_id'],
            'indirect_income_ledger_id' => $data['ledger_id'],
            's_fa_ledger_entry_ids' => $fa_ledger_entry_ids
        );
               
        $receiptId = parent::create($dataToInsert); 
        
        $log = $this->getLoggerService();
        $info = 'Indirect Income Cash Receipt created with receipt id = '. 
                                                                    $receiptId;
        $log->info($info);
        
        return $receiptId;
    }
    
    /**
     * Create a finance  Indirect Income Cheque Receipt
     * @param array $data with keys 
     * @return int receipt ID 
     */
    public function createIndirectIncomeChequeReceipt($data = array())
    {
        $table = $this->getTable();
        
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $notes ='Receipt : mode - Cheque Type - Indirect Income Cheque Receipt'; 
              
        $ledgerEntryIds = array(
            '0' => $this->incomeLegerEntry($data['ledger_id'], 
                                            $data['amount'], $notes),
            '1' => $this->bankLegerEntry($data['bank_account_id'],
                                         $data['amount'], $notes)
        );
        
        $ledgerEntryIds = serialize($ledgerEntryIds);
        
        $dataToInsert = array(
            'mode' => self::DD_CHECK,
            'type' => self::RECEIPT_TOWARDS_INDIRECT_INCOME,
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'indirect_income_ledger_id' => $data['ledger_id'],
            'mode_account_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds
        );
        
        $this->_receiptId = parent::create($dataToInsert); 
        
        $dataToInsertReceiptBank = array(
             'receipt_id' => $this->_receiptId,
             'bank_name' => $data['bank_name'],
             'bank_branch' => $data['bank_branch'],
             'instrument_number' =>$data['instrument_number'],
             'instrument_account_no' => $data['instrument_account_no'],
             'instrument_date' => $date->getTimestamp(),
          );
        $receiptBankModel =  new Core_Model_Finance_Receipt_Bank;
        $result = $receiptBankModel->create($dataToInsertReceiptBank);
        
        $log = $this->getLoggerService();
        $info = 'Indirect Income Cheque Receipt created with receipt id = '. 
                                                             $this->_receiptId;
        $log->info($info);
        
        return $this->_receiptId;
    }
    
    /**
     * @param array $data
     * @return int
     */
    public function editIndirectIncomeChequeReceipt($data = array())
    {   
        $receiptRecord = $this->fetch();
        $lederEntryIdToDelete = 
                           unserialize($receiptRecord['s_fa_ledger_entry_ids']);
       
        $LedgerEntyModel = new Core_Model_Finance_Ledger_Entry;
        $result = $LedgerEntyModel->deleteByIds($lederEntryIdToDelete);
        
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        $notes = 'Receipt : mode - cheque Type - Indirect Income Cheque Receipt'; 
        $ledgerEntryIds = array(
            '0' => $this->incomeLegerEntry($data['ledger_id'], 
                                                  $data['amount'], $notes),
            '1' => $this->bankLegerEntry($data['bank_account_id'],
                                                  $data['amount'], $notes)
        );
        $ledgerEntryIds = serialize($ledgerEntryIds);
        
        $dataToInsert = array(
            'mode' => self::DD_CHECK,
            'type' => self::RECEIPT_TOWARDS_INDIRECT_INCOME,
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'branch_id' => $data['branch_id'],
            'indirect_income_ledger_id' => $data['ledger_id'],
            'mode_account_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds
        );
          
        $table = $this->getTable();
        
        $where = $table->getAdapter()
                    ->quoteInto('receipt_id = ?', $this->_receiptId);
        
        $result = $table->update($dataToInsert, $where);
        
        $dataToInsertReceiptBank = array(
             'receipt_id' => $this->_receiptId,
             'bank_name' => $data['bank_name'],
             'bank_branch' => $data['bank_branch'],
             'instrument_number' =>$data['instrument_number'],
             'instrument_account_no' => $data['instrument_account_no'],
             'instrument_date' => $date->getTimestamp(),
              );
              
        $receiptBankModel =  new Core_Model_Finance_Receipt_Bank;
        $result = $receiptBankModel->edit($dataToInsertReceiptBank,
                                                             $this->_receiptId);
        $log = $this->getLoggerService();
        $info = 'Indirect Income Cash Receipt edited with receipt id = '. 
                                                            $this->_receiptId;
        $log->info($info);
        
        return $result;
    }
    
    /**
     * @param array $data
     * @return int
     */
    public function editIndirectIncomeCashReceipt($data = array())
    {   
        $receiptRecord = $this->fetch();
        $lederEntryIdToDelete = 
                           unserialize($receiptRecord['s_fa_ledger_entry_ids']);
        $LedgerEntyModel = new Core_Model_Finance_Ledger_Entry;
        for($i = 0; $i <= sizeof($lederEntryIdToDelete)-1; $i += 1) {
            $result = $LedgerEntyModel->deleteById($lederEntryIdToDelete[$i]);
        }
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp(); 
        
        $notes = 'Receipt : mode - cash Type - Indirect Income Cash Receipt';
        
        $ledgerEntryIds = array(
            '0' => $this->incomeLegerEntry($data['ledger_id'], 
                                           $data['amount'], $notes),
            '1' => $this->cashLegerEntry($data['cashaccount_id'],
                                            $data['amount'], $notes)
        );
        $fa_ledger_entry_ids = serialize($ledgerEntryIds);
        $dataToUpdate = array(
            'mode' => self::CASH,
            'type' => self::RECEIPT_TOWARDS_INDIRECT_INCOME,
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'branch_id' => $data['branch_id'],
            'mode_account_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $fa_ledger_entry_ids
        );
        $table = $this->getTable();
        
        $where = $table->getAdapter()
                    ->quoteInto('receipt_id = ?', $this->_receiptId);
        
        $result = $table->update($dataToUpdate, $where);
        
        $dataToInsertReceiptBank = array(
             'receipt_id' => $this->_receiptId,
             'bank_name' => $data['bank_name'],
             'bank_branch' => $data['bank_branch'],
             'instrument_account_no' => $data['instrument_account_no'],
             'instrument_date' => $date->getTimestamp(),
              );
              
        $receiptBankModel =  new Core_Model_Finance_Receipt_Bank;
        $result = $receiptBankModel->edit($dataToInsertReceiptBank,
                                                             $this->_receiptId);
        $log = $this->getLoggerService();
        $info = 'Indirect Income Cash Receipt edited with receipt id = '. 
                                                              $this->_receiptId;
        $log->info($info);
        return $result;
    }
    
    /**
     * @param Receipt Id and Bankaccount Id
     * @return array of amount
     */
    public function getAmountByReceiptIdAndBankAccountId($receiptId, 
                                                            $bankAccountId)
    {  
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('receipt_id = ?', $receiptId)
                    ->where('mode_account_id = ?', $bankAccountId);
        $result = $table->fetchRow($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        return $result['amount'];
    }

    /**
     * @return string PDF file location 
     */
    public function getPdfFileLocation()
    {
        $pdf = new Core_Model_Finance_Receipt_Pdf();
        $pdf->setModel($this);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/documents/receipt/receipt_' .
                                                     $this->_receiptId . '.pdf';
        $pdf->Output($pdfPath, 'F');
        return $pdfPath;
    }
    
    /**
     * @return string PDF file location 
     */
    public function fetchReceiptsByDate($date)
    {
        $date = new Zend_Date($date);
        $startDate = $date->getTimestamp();
                
        $endDate = $date->addDay(1);
        $endDate = $endDate->getTimestamp();
        
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where("date BETWEEN '$startDate' and '$endDate'");
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    
}



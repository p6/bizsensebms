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
class Core_Model_Finance_Payment extends Core_Model_Abstract
{
    /**
     * @var the Payment ID
     */
    protected $_paymentId;
    
    const FROM_TYPE = 'payment from type';
    const PAYMENT_TYPE_CASH = 1;
    const PAYMENT_TYPE_CHEQUE = 2;   

    const PAYMENT_TO_SUNDRY_CREDITORS = 1;
    const PAYMENT_TOWARDS_EXPENSES = 2; 
    const PAYMENT_TOWARDS_TDS = 3; 
    const PAYMENT_TOWARDS_TAX = 4;
    const PAYMENT_TOWARDS_SALARY = 5;
    const PAYMENT_TOWARDS_ADVANCE = 6;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_Payment';

    /**
     * @var to store date for transaction
     */
    protected $_transactionDate;
    
    /**
     * @param paymentId
     */
    public function __construct($paymentId = null)
     {
        if (is_numeric($paymentId)) {  
            $this->_paymentId = $paymentId;
        }
        parent::__construct();
     }
     
    /**
     * @param int ledgerId
     * @return fluent interface
     */
    public function setPaymentId($paymentId)
    {
        $this->_paymentId = $paymentId;
        return $this;
    }

    /**
     * @return int the Payment ID
     */
    public function getPaymentId()
    {
        return $this->_paymentId;
    }


    /**
     * Create a Cash payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createCashPayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $vendorModel = new Core_Model_Finance_Vendor($data['vendor_id']);
        $vendorName = $vendorModel->getName();
        $notes = 'Cash payment to '.$vendorName;
        $ledgerEntryIds = array(
           '0' => $this->vendorLegerEntry($data['vendor_id'],
                                    $data['amount'], $notes),
           '1' => $this->cashAccountLegerEntry($cashaccountModel->getLedgerId(),
                                              $data['amount'], $notes),
        );
        $ledgerEntryIds = serialize($ledgerEntryIds);
       
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TO_SUNDRY_CREDITORS,
            'type_id' => $data['vendor_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $this->_paymentId = parent::create($dataToInsert);
        
        $log = $this->getLoggerService();
        $info = 'Cash payment created with payment id = '. $this->_paymentId;
        $log->info($info);
        
        return $this->_paymentId;
    }   
    
    /**
     * Create a Cheque payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createChequePayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $vendorModel = new Core_Model_Finance_Vendor($data['vendor_id']);
        $vendorName = $vendorModel->getName();
        $notes = "Cheque Payment to ". $vendorName. 'Cheque Number - '. 
                                               $data['instrument_number'];
        $ledgerEntryIds['0'] = $this->vendorLegerEntry($data['vendor_id'],
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
              $bankaccountModel->getLedgerId(), $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);
               
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TO_SUNDRY_CREDITORS,
            'type_id' => $data['vendor_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $date = new Zend_Date($data['instrument_date']);
       
        $paymentId = parent::create($dataToInsert); 
        
        $dataToInsertPaymentBank = array(
            'payment_id' => $paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->create($dataToInsertPaymentBank);
        
        $log = $this->getLoggerService();
        $info = 'Cheque payment created with payment id = '. $this->_paymentId;
        $log->info($info);
        
        return $paymentId;
    }  
    
    /**
     * Create a Cash payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createExpensesCashPayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $notes = "Expenses Cash Payment";
        
        $ledgerEntryIds['0'] = $this->expensesLegerEntry($data['ledger_id'],
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->cashAccountLegerEntry(
                              $cashaccountModel->getLedgerId(), 
                                                $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);
               
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_EXPENSES,
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            'indirect_expense_ledger_id' => $data['ledger_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
       
        $log = $this->getLoggerService();
        $info = 'Expenses Cash payment created with payment id = '. 
                                                            $this->_paymentId;
        $log->info($info);
        
        return  parent::create($dataToInsert); 
    }
    
    /**
     * Create a Cheque payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createExpensesChequePayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $notes = "Expenses Cheque Payment Cheque Number - ".
                                                $data['instrument_number'];
        
        $ledgerEntryIds['0'] = $this->expensesLegerEntry($data['ledger_id'], 
                                                    $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
                              $bankaccountModel->getLedgerId(),
                                                    $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);
      
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_EXPENSES,
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            'indirect_expense_ledger_id' => $data['ledger_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $date = new Zend_Date($data['instrument_date']);
       
        $paymentId = parent::create($dataToInsert); 
        
        $dataToInsertPaymentBank = array(
            'payment_id' => $paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->create($dataToInsertPaymentBank);
        
        $log = $this->getLoggerService();
        $info = 'Expenses Cheque payment created with payment id = '.$paymentId;
        $log->info($info);
        return $paymentId;
    }  
    
    /**
     * Create a Cash payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createTdsCash($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $notes = "TDS Cash payment";
        
        $cashAccountAmount = $data['amount'] - $data['tax_amount'];
        $ledgerEntryIds['0'] = $this->expensesLegerEntry($data['ledger_id'],
                                                     $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->cashAccountLegerEntry(
              $cashaccountModel->getLedgerId(), $cashAccountAmount, $notes);
        $ledgerEntryIds['2'] = $this->tdsLegerEntry($data['tds_ledger_id'],
                                               $data['tax_amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_TDS,
            'type_id' => $data['tds_ledger_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            'indirect_expense_ledger_id' => $data['ledger_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $this->_paymentId = parent::create($dataToInsert);
        
        $dataToInsertTds = array(
            'payment_id' => $this->_paymentId,
            'tds_amount' => $data['tax_amount']
        );
        
        $tdsModel = new Core_Model_Finance_Payment_Tds;
        $tdsModel->create($dataToInsertTds);
        
        $log = $this->getLoggerService();
        $info = 'TDS Cash payment created with payment id = '. $this->_paymentId;
        $log->info($info);
        
        return $this->_paymentId; 
    }  
    
    /**
     * Create a Cheque payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createTdsChequePayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $notes = "TDS cheque payment Cheque Number - ".
                                                $data['instrument_number'];
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $bankAccountAmount = $data['amount'] - $data['tax_amount'];
        $ledgerEntryIds['0'] = $this->expensesLegerEntry($data['ledger_id'], 
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
                $bankaccountModel->getLedgerId(), $bankAccountAmount, $notes);
        $ledgerEntryIds['2'] = $this->tdsLegerEntry($data['tds_ledger_id'],
                                                 $data['tax_amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_TDS,
            'type_id' => $data['tds_ledger_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            'indirect_expense_ledger_id' => $data['ledger_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $date = new Zend_Date($data['instrument_date']);
       
        $this->_paymentId = parent::create($dataToInsert); 
        
        $dataToInsertPaymentBank = array(
            'payment_id' => $this->_paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->create($dataToInsertPaymentBank);
        
        $dataToInsertTds = array(
            'payment_id' => $this->_paymentId,
            'tds_amount' => $data['tax_amount']
        );
        
        $tdsModel = new Core_Model_Finance_Payment_Tds;
        $tdsModel->create($dataToInsertTds);
        
        $log = $this->getLoggerService();
        $info = 'TDS Cheque payment created with payment id = '.$paymentId;
        $log->info($info);
        
        return $paymentId;
    }  
    
    /**
     * Create a Cash payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createTaxCashPayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                      $data['cashaccount_id']);
        $notes = "Tax cash payment";
        $ledgerEntryIds['0'] = $this->cashAccountLegerEntry(
              $cashaccountModel->getLedgerId(), $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->taxLegerEntry($data['tds_ledger_id'],
                                                   $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);
        
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_TAX,
            'type_id' => $data['tds_ledger_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $this->_paymentId = parent::create($dataToInsert); 
        $log = $this->getLoggerService();
        $info = 'Tax Cash payment created with payment id = '. $this->_paymentId;
        $log->info($info);
        
        return $this->_paymentId;
    }  
    
    /**
     * Create a Cheque payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createTaxChequePayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $notes = "Tax cheque payment Cheque Number - ".
                                                $data['instrument_number'];
        $ledgerEntryIds['0'] = $this->taxLegerEntry($data['tds_ledger_id'],
                                                $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
             $bankaccountModel->getLedgerId(),$data['amount'], $notes);
       
        $ledgerEntryIds = serialize($ledgerEntryIds);
        
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_TAX,
            'type_id' => $data['tds_ledger_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $date = new Zend_Date($data['instrument_date']);
       
        $paymentId = parent::create($dataToInsert); 
        
        $dataToInsertPaymentBank = array(
            'payment_id' => $paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->create($dataToInsertPaymentBank);
        
        $log = $this->getLoggerService();
        $info = 'Tax Cheque payment created with payment id = '.$paymentId;
        $log->info($info);
        
        return $paymentId;
    }  
    
    /**
     * Create a Cash payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createSalaryCashPayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $notes = "Salary cash payment";
        $ledgerEntryIds = array(
           '0' => $this->salaryLegerEntry($data['employee_id'],
                                        $data['amount'], $notes),
           '1' => $this->cashAccountLegerEntry($cashaccountModel->getLedgerId(),
                                          $data['amount'], $notes),
        );
        
        $ledgerEntryIds = serialize($ledgerEntryIds);
        
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_SALARY,
            'type_id' => $data['employee_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $this->_paymentId= parent::create($dataToInsert);
        $log = $this->getLoggerService();
        $info = 'Salary Cash payment created with payment id = '. $this->_paymentId;
        $log->info($info);
        
        return $this->_paymentId;
    }   
    
    /**
     * Create a Cheque payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createSalaryChequePayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $notes = "Salary cheque payment Cheque Number - ".
                                                $data['instrument_number'];
        $ledgerEntryIds['0'] = $this->salaryLegerEntry($data['employee_id'],
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
                           $bankaccountModel->getLedgerId(),
                                                $data['amount'], $notes);
       
        $ledgerEntryIds = serialize($ledgerEntryIds);
        
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_SALARY,
            'type_id' => $data['employee_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $date = new Zend_Date($data['instrument_date']);
       
        $paymentId = parent::create($dataToInsert); 
        
        $dataToInsertPaymentBank = array(
            'payment_id' => $paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->create($dataToInsertPaymentBank);
        
        $log = $this->getLoggerService();
        $info = 'Salary Cheque payment created with payment id = '. $paymentId;
        $log->info($info);
        
        return $paymentId;
    }  
    
    /**
     * Create a Cash payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createAdvanceCashPayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $notes = "Advance cash payment";
        $ledgerEntryIds = array(
           '0' => $this->advanceLegerEntry($data['employee_id'],
                                        $data['amount'], $notes),
           '1' => $this->cashAccountLegerEntry($cashaccountModel->getLedgerId(),
                                          $data['amount'], $notes),
        );
        
        $ledgerEntryIds = serialize($ledgerEntryIds);
        
        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_ADVANCE,
            'type_id' => $data['employee_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $this->_paymentId =  parent::create($dataToInsert); 
        
        $log = $this->getLoggerService();
        $info = 'Advance Cash payment created with payment id = '. 
                                                        $this->_paymentId;
        $log->info($info);
        
        return $this->_paymentId;
    }   
    
    /**
     * Create a Cheque payment record
     * @param array $data with keys 
     * @return int Payment ID 
     */
    public function createAdvanceChequePayment($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $notes = "Advance cheque payment Cheque Number - ".
                                                $data['instrument_number'];
        $ledgerEntryIds['0'] = $this->advanceLegerEntry($data['employee_id'],
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
                           $bankaccountModel->getLedgerId(),
                                                $data['amount'], $notes);
       
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToInsert = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_ADVANCE,
            'type_id' => $data['employee_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $date = new Zend_Date($data['instrument_date']);
       
        $paymentId = parent::create($dataToInsert); 
        
        $dataToInsertPaymentBank = array(
            'payment_id' => $paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->create($dataToInsertPaymentBank);
        
        $log = $this->getLoggerService();
        $info = 'Advance Cheque payment created with payment id = '. 
                                                        $this->_paymentId;
        $log->info($info);
        
        return $paymentId;
    }  
    /**
     * @param vendorId, amount
     * @return Vendor Ledger Entry Id
     */
    protected function vendorLegerEntry($vendorId, $amount, $notes) 
    {
        $vendorModel =  new Core_Model_Finance_Vendor($vendorId);
        $vendorLedgerId = $vendorModel->getLedgerId();
        $dataToInsert = array(
            'debit' => $amount,
            'credit' => "0",
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionDate,
            'fa_ledger_id' => $vendorLedgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledgerId, amount
     * @return Cash account Ledger Entry Id
     */
    protected function cashAccountLegerEntry($ledgerId, $amount, $notes) 
    {
        $dataToInsert = array(
            'debit' => "0",
            'credit' => $amount,
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionDate,
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledgerId, amount
     * @return Bank account Ledger Entry Id
     */
    protected function bankAccountLegerEntry($ledgerId, $amount, $notes) 
    {
        $dataToInsert = array(
            'debit' => "0",
            'credit' => $amount,
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionDate,
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledgerId, amount
     * @return Expenses Ledger Entry Id
     */
    protected function expensesLegerEntry($ledgerId, $amount, $notes) 
    {
        $dataToInsert = array(
            'debit' => $amount,
            'credit' => "0",
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionDate,
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledgerId, amount
     * @return TDS Ledger Entry Id
     */
    protected function tdsLegerEntry($ledgerId, $amount, $notes) 
    {
        $dataToInsert = array(
            'debit' => "0",
            'credit' => $amount,
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionDate,
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledgerId, amount
     * @return Tax Ledger Entry Id
     */
    protected function taxLegerEntry($ledgerId, $amount, $notes) 
    {
        $dataToInsert = array(
            'debit' => $amount,
            'credit' => "0",
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionDate,
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledgerId, amount
     * @return Salary Ledger Entry Id
     */
    protected function salaryLegerEntry($userId, $amount, $notes) 
    {
        $profileModel = new Core_Model_User_Profile($userId);
        $ledgerId = $profileModel->getLedgerId();
        $dataToInsert = array(
            'debit' => $amount,
            'credit' => "0",
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionDate,
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledgerId, amount
     * @return Salary Ledger Entry Id
     */
    protected function advanceLegerEntry($userId, $amount, $notes) 
    {
        $profileModel = new Core_Model_User_Profile($userId);
        $ledgerId = $profileModel->getAdvanceLedgerId();
        $dataToInsert = array(
            'debit' => $amount,
            'credit' => "0",
            'notes' => $notes,
            'transaction_timestamp' => $this->_transactionDate,
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * Fetches a single Payment record and related 
     * payment bank information from db 
     * Based on currently set paymentId 
     * @return array of Payment record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()->where(
            'payment_id = ?', $this->_paymentId
        );
        $result = $table->fetchRow($select)->toArray();
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankRecord = $paymentBankModel->fetchbyPaymentId(
                                                            $this->_paymentId);
        if($paymentBankRecord != null) {
          $result['instrument_number'] = $paymentBankRecord['instrument_number'];
          $result['instrument_date'] = $paymentBankRecord['instrument_date'];
        }
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editCashPayment($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $cashAccountName = $cashaccountModel->getName();
        $notes = 'Cash payment to '.$cashAccountName;
        $ledgerEntryIds = array(
           '0' => $this->vendorLegerEntry($data['vendor_id'],
                                    $data['amount'], $notes),
           '1' => $this->cashAccountLegerEntry($cashaccountModel->getLedgerId(),
                                              $data['amount'], $notes),
        );
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToUpdate = array(
            'notes' => $data['notes'],
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'type' => self::PAYMENT_TO_SUNDRY_CREDITORS,
            'type_id' => $data['vendor_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editChequePayment($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $vendorModel = new Core_Model_Finance_Vendor($data['vendor_id']);
        $vendorName = $vendorModel->getName();
        $notes = "Cheque Payment to ". $vendorName."Cheque Number - ".
                                                $data['instrument_number'];
        $ledgerEntryIds['0'] = $this->vendorLegerEntry($data['vendor_id'],
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
              $bankaccountModel->getLedgerId(), $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);
        
        $dataToUpdate = array(
            'notes' => $data['notes'],
            'amount' => $data['amount'],
            'date' => $date->getTimestamp(),
            'type' => self::PAYMENT_TO_SUNDRY_CREDITORS,
            'type_id' => $data['vendor_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        
        $date = new Zend_Date($data['instrument_date']);
        
        $dataToUpdatePaymentBank = array(
            'payment_id' => $this->_paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->edit($dataToUpdatePaymentBank);
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editExpensesCashPayment($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $notes = "Expenses Cash Payment";
        
        $ledgerEntryIds['0'] = $this->expensesLegerEntry($data['ledger_id'],
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->cashAccountLegerEntry(
                     $cashaccountModel->getLedgerId(), $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_EXPENSES,
            'type_id' => $data['vendor_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'indirect_expense_ledger_id' => $data['ledger_id'],
            'mode_id' => $data['cashaccount_id'],
            'branch_id' => $data['branch_id']
        );
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editExpensesChequePayment($data = array())
    {       
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
          
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $notes = "Expenses Cheque Payment Cheque Number - ".
                                                $data['instrument_number'];
        
        $ledgerEntryIds['0'] = $this->expensesLegerEntry($data['ledger_id'], 
                                                    $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
                              $bankaccountModel->getLedgerId(),
                                                    $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_EXPENSES,
            'type_id' => $data['vendor_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'indirect_expense_ledger_id' => $data['ledger_id'],
            'mode_id' => $data['bank_account_id'],
            'branch_id' => $data['branch_id']
        );
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        
        $date = new Zend_Date($data['instrument_date']);
        
        $dataToUpdatePaymentBank = array(
            'payment_id' => $this->_paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->edit($dataToUpdatePaymentBank);
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editTdsCash($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $notes = "TDS Cash payment";
        $cashAccountAmount = $data['amount'] - $data['tax_amount'];
        $ledgerEntryIds['0'] = $this->expensesLegerEntry($data['ledger_id'],
                                                     $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->cashAccountLegerEntry(
           $cashaccountModel->getLedgerId(), $cashAccountAmount, $notes);
        $ledgerEntryIds['2'] = $this->tdsLegerEntry($data['tds_ledger_id'],
                                               $data['tax_amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_TDS,
            'type_id' => $data['vendor_id'],
            'type_id' => $data['tds_ledger_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            'indirect_expense_ledger_id' => $data['ledger_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        
        $dataToInsertTds = array(
            'payment_id' => $this->_paymentId,
            'tds_amount' => $data['tax_amount']
        );
        
        $tdsModel = new Core_Model_Finance_Payment_Tds;
        $tdsModel->edit($dataToInsertTds);
        
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editTdsCheque($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $notes = "TDS cheque payment Cheque Number - ".
                                                $data['instrument_number'];
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
        $bankAccountAmount = $data['amount'] - $data['tax_amount'];
        $ledgerEntryIds['0'] = $this->expensesLegerEntry($data['ledger_id'], 
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
                 $bankaccountModel->getLedgerId(), $bankAccountAmount, $notes);
        $ledgerEntryIds['2'] = $this->tdsLegerEntry($data['tds_ledger_id'],
                                                 $data['tax_amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);
 
        $dataToUpdate = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_TDS,
            'type_id' => $data['tds_ledger_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            'indirect_expense_ledger_id' => $data['ledger_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        
        $dataToUpdatePaymentBank = array(
            'payment_id' => $this->_paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->edit($dataToUpdatePaymentBank);
        
        $dataToInsertTds = array(
            'payment_id' => $this->_paymentId,
            'tds_amount' => $data['tax_amount']
        );
        
        $tdsModel = new Core_Model_Finance_Payment_Tds;
        $tdsModel->edit($dataToInsertTds);
        
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editTaxCash($data = array())
    {    
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
             
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
                
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                    $data['cashaccount_id']);
        
        $notes = "Tax cash payment";
        $ledgerEntryIds['0'] = $this->cashAccountLegerEntry(
              $cashaccountModel->getLedgerId(), $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->taxLegerEntry($data['tds_ledger_id'],
                                                   $data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_TAX,
            'type_id' => $data['tds_ledger_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editTaxCheque($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                      $data['bank_account_id']);
                                                      
        $notes = "Tax cheque payment Cheque Number - ".
                                                $data['instrument_number'];
        $ledgerEntryIds['0'] = $this->taxLegerEntry($data['tds_ledger_id'],
                                                $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
             $bankaccountModel->getLedgerId(),$data['amount'], $notes);
        $ledgerEntryIds = serialize($ledgerEntryIds);
 
        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_TAX,
            'type_id' => $data['tds_ledger_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        
        $dataToUpdatePaymentBank = array(
            'payment_id' => $this->_paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->edit($dataToUpdatePaymentBank);
        
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editSalaryCash($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
                
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $notes = "Salary cash payment";
        $ledgerEntryIds = array(
           '0' => $this->salaryLegerEntry($data['employee_id'],
                                        $data['amount'], $notes),
           '1' => $this->cashAccountLegerEntry($cashaccountModel->getLedgerId(),
                                          $data['amount'], $notes),
        );
        
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_SALARY,
            'type_id' => $data['employee_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editSalaryCheque($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                    $data['bank_account_id']);
                                                      
        $notes = "Salary cheque payment Cheque Number - ".
                                                $data['instrument_number'];
        $ledgerEntryIds['0'] = $this->salaryLegerEntry($data['employee_id'],
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
                           $bankaccountModel->getLedgerId(),
                                                $data['amount'], $notes);
       
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_SALARY,
            'type_id' => $data['employee_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        
        $dataToUpdatePaymentBank = array(
            'payment_id' => $this->_paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->edit($dataToUpdatePaymentBank);
        
        return $result;
    }
    
    
    public function getLedgerEntryModel()
    {
        if (null === $this->_ledgerEntryModel) {
            $this->_ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        }
        return $this->_ledgerEntryModel;
    }
    
    /**
     * deletes a row in table based on currently set bankAccountId
     * @return int
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $log = $this->getLoggerService();
        $info = 'Payment deleted with payment id = '. $this->_paymentId;
        $log->info($info);
        return $table->delete($where);
    }
    
    /**
     * @param Receipt Id and Bankaccount Id
     * @return array of amount
     */
    public function getAmountByPaymentIdAndBankAccountId($paymentId, 
                                                                $bankAccountId)
    {  
        $table = $this->getTable();
               
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('payment_id = ?', $paymentId)
                    ->where('mode = ?', '2')
                    ->where('mode_id = ?', $bankAccountId);
        $result = $table->fetchRow($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        
        if ($result['type']== self::PAYMENT_TOWARDS_TDS) {
            $paymentTdsModel = new Core_Model_Finance_Payment_Tds;
            $paymentTdsRecord = $paymentTdsModel->fetchbyPaymentId($paymentId);
            $result['amount'] = $result['amount'] - 
                                               $paymentTdsRecord['tds_amount'];
        }
              
        return $result['amount'];
    }
    
    /**
     * @return array 
     */
    public function fetchPaymentsByDate($date)
    {
        $date = new Zend_Date($date);
        $startDate = $date->getTimestamp();
                
        $endDate = $date->addDay(1);
        $endDate = $endDate->getTimestamp();
        
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where("created BETWEEN '$startDate' and '$endDate'");
                    
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        
        return $result;
    }
    
    /**
     * @return array  
     */
    public function selectPayment($data)
    {
        $mode = $data['mode'];
        $type = $data['type'];
       
        /**
         *  CASH && PAYMENT_TO_SUNDRY_CREDITORS 
         */
        if ($mode == 1 && $type == 1) {
            return 'sccashpayment';
        }
        /**
         *  CHEQUE && PAYMENT_TO_SUNDRY_CREDITORS
         */
        if ($mode == 2 && $type == 1) {
            return 'scchequepayment';
        }
                
        /**
         * CASH && PAYMENT_TOWARDS_EXPENSES
         */
        if ($mode == 1 && $type == 2) {
            return 'ecashpayment';
        }
        
        /**
         * CHEQUE && PAYMENT_TOWARDS_EXPENSES
         */
        if ($mode == 2 && $type == 2) {
            return 'echequepayment';
        }
        
        /**
         * CASH && PAYMENT_TOWARDS_TDS
         */
        if ($mode == 1 && $type == 3) {
            return 'tdscash';
        }
        
        /**
         * CHEQUE && PAYMENT_TOWARDS_TDS
         */
        
        if ($mode == 2 && $type == 3) {
            return 'tdscheque';
        }
        
        /**
         * CASH && PAYMENT_TOWARDS_TAX
         */
        if ($mode == 1 && $type == 4) {
            return 'taxcash';
        }
        
        /**
         * CHEQUE && PAYMENT_TOWARDS_TAX
         */
        if ($mode == 2 && $type == 4) {
            return 'taxcheque';
        }
        
        /**
         * CASH && PAYMENT_TOWARDS_SALARY
         */
        if ($mode == 1 && $type == 5) {
            return 'salarycashpayment';
        }
        
        /**
         * CHEQUE && PAYMENT_TOWARDS_SALARY
         */
        if ($mode == 2 && $type == 5) {
            return 'salarychequepayment';
        }
        
        /**
         * CASH && PAYMENT_TOWARDS_ADVANCE
         */
        if ($mode == 1 && $type == 6) {
            return 'advancecashpayment';
        }
        
        /**
         * CHEQUE && PAYMENT_TOWARDS_ADVANCE
         */
        if ($mode == 2 && $type == 6) {
            return 'advancechequepayment';
        }
       
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editAdvanceCash($data = array())
    {        
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
         
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
                
        $cashaccountModel = new Core_Model_Finance_CashAccount(
                                                       $data['cashaccount_id']);
        $notes = "Advance cash payment";
        $ledgerEntryIds = array(
           '0' => $this->advanceLegerEntry($data['employee_id'],
                                        $data['amount'], $notes),
           '1' => $this->cashAccountLegerEntry($cashaccountModel->getLedgerId(),
                                          $data['amount'], $notes),
        );
        
        $ledgerEntryIds = serialize($ledgerEntryIds);

        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_ADVANCE,
            'type_id' => $data['employee_id'],
            'mode' => self::PAYMENT_TYPE_CASH,
            'mode_id' => $data['cashaccount_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        return $result;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function editAdvanceCheque($data = array())
    {         
        $date = new Zend_Date($data['date']);
        $this->_transactionDate = $date->getTimestamp();
        
        $paymentData = $this->fetch();
        $ledgerEntryIds = unserialize($paymentData['s_fa_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                    $data['bank_account_id']);
                                                      
        $notes = "Advance cheque payment Cheque Number - ".
                                                $data['instrument_number'];
        $ledgerEntryIds['0'] = $this->advanceLegerEntry($data['employee_id'],
                                                 $data['amount'], $notes);
        $ledgerEntryIds['1'] = $this->bankAccountLegerEntry(
                           $bankaccountModel->getLedgerId(),
                                                $data['amount'], $notes);
       
        $ledgerEntryIds = serialize($ledgerEntryIds);
 
        $dataToUpdate = array(
            'notes' => $data['notes'],
            'date' => $date->getTimestamp(),
            'amount' => $data['amount'],
            'type' => self::PAYMENT_TOWARDS_ADVANCE,
            'type_id' => $data['employee_id'],
            'mode' => self::PAYMENT_TYPE_CHEQUE,
            'mode_id' => $data['bank_account_id'],
            's_fa_ledger_entry_ids' => $ledgerEntryIds,
            'branch_id' => $data['branch_id']
        );
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $this->_paymentId);
        $result = $table->update($dataToUpdate, $where);
        
        $dataToUpdatePaymentBank = array(
            'payment_id' => $this->_paymentId,
            'instrument_number' => $data['instrument_number'],
            'instrument_date' => $date->getTimestamp(),
        );
        
        $paymentBankModel = new Core_Model_Finance_Payment_Bank;
        $paymentBankId = $paymentBankModel->edit($dataToUpdatePaymentBank);
        
        return $result;
    }
}


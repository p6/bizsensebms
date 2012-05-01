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
class Core_Model_Finance_Payslip extends Core_Model_Abstract
{
    const EARNING_FIELDS = '1';
    const DEDUCTION_TAX_FIELDS = '2';
    const DEDUCTION_NON_TAX_FIELDS = '3';
    /**
     * @var the Payslip ID
     */
	 protected $_payslipId;
	 
    /**
     * @var the Payslip Field Model
     */
	 protected $_payslipFieldModel;
	 
	/**
     * @var the Payslip Item Model
     */
	 protected $_payslipItemModel;
	 
	/**
     * @var the tansaction time
     */
	 protected $_transactionTime;
	 
	/**
     * @var object ledger entry model
     */
    protected $_ledgerEntryModel;
    
    /**
     * @param payslipId
     */
    public function setPayslipId($payslipId)
    {
        $this->_payslipId = $payslipId;
        return $this;
    }

    /**
     * @return int payslipId
     */
    public function getPayslipId()
    {
        return $this->_payslipId;
    }
    
    /**
     * Create a Payslip  
     * @param array $data with keys 
     * @return int Payslip  ID 
     */
    public function create($data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        $payslipData = array(
            'employee_id' => $data['employee_id'],
            'date' =>  $this->_transactionTime,
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId()
        );
        $this->_payslipId = parent::create($payslipData);
        
        $this->ledgerEntries($data);
        
        unset ($data['employee_id'], $data['date'], $data['ledger_id'], 
                                                            $data['submit']);
               
        $payslipItemModel = new Core_Model_Finance_Payslip_Item;
        foreach ($data as $machine_name => $amount) {
            $itemData['payslip_field_id'] = 
             $this->_payslipFieldModel->getFieldsIdByMachineName($machine_name);
            $itemData['amount'] = $amount;
            $itemData['payslip_id'] = $this->_payslipId;
            $payslipItemId = $payslipItemModel->create($itemData);
        }
        
        $log = $this->getLoggerService();
        $info = 'Payslip created with payslip id = '. $this->_payslipId;
        $log->info($info);
         
        return $this->_payslipId;
    }
    
    /**
     * pasyslip ledger entries and update ledger entry ids to 
     * table(s_fa_ledger_ids)
     * return int
     */
    public function ledgerEntries($data)
    {
        $ledgerEntryIds['0'] = $this->employeeLedgerEntry($data);
        $ledgerEntryIds['1'] = $this->salaryLedgerEntry($data);
        $employeeId = $data['employee_id'];
        unset ($data['employee_id'], $data['date'], $data['ledger_id'], 
                                                              $data['submit']);
        $taxLedgerEntries = $this->taxLedgerEntry($data);
        if ($taxLedgerEntries) {
            for($i = 0; $i <= sizeof($taxLedgerEntries)-1; $i += 1) {
                array_push($ledgerEntryIds, $taxLedgerEntries[$i]);
            }
        }
        foreach ($data as $name => $amount) {
            if ($name == 'advance') {
                $ledgerEntryIds = 
                                $this->advanceLegerEntry($employeeId, $amount);
                array_push($ledgerEntryIds, $ledgerEntryIds);
            }
        } 
        $dataToUpdate['s_fa_ledger_ids'] = serialize($ledgerEntryIds);
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('payslip_id = ?', 
                                                           $this->_payslipId);
        $result = $table->update($dataToUpdate, $where);
        return $result;
    }
    
    /**
     * @param array of items with amount
     * @return total salary
     */
     public function calculateSalaryAmount($data) 
     {
        $totalEarningFields = 0;
        $totalDeductionTaxFields = 0;
        $totalDeductionNonTaxFields = 0;
        foreach ($data as $name => $amount) {
           $type = $this->_payslipFieldModel->getTypeByMachineName($name);
           
           if ($type == self::EARNING_FIELDS) {
               $totalEarningFields += $amount;
           }
           
           if ($type == self::DEDUCTION_TAX_FIELDS) {
               $totalDeductionTaxFields += $amount;
           }
           
           if ($type == self::DEDUCTION_NON_TAX_FIELDS) {
               $totalDeductionNonTaxFields += $amount;
           }
        }
        return $totalEarningFields - ($totalDeductionTaxFields + 
                                                $totalDeductionNonTaxFields);
     }
     /**
     * @param array of items with amount
     * @return total salary
     */
     public function getPayableSalaryAmount() 
     {
        $totalEarningFields = 0;
        $totalDeductionTaxFields = 0;
        $totalDeductionNonTaxFields = 0;
        $payslipItemModel = new Core_Model_Finance_Payslip_Item;
        $payslipItemRecord = $payslipItemModel->getItemByPayslipId(
                                                        $this->_payslipId);
                                                        
        for ($i = 0; $i < count($payslipItemRecord); $i++ ) {
           $this->_payslipFieldModel->setPayslipFieldId(
                        $payslipItemRecord[$i]['payslip_field_id']);
           $payslipFieldRecord = $this->_payslipFieldModel->fetch();
                      
           if ($payslipFieldRecord['type'] == self::EARNING_FIELDS) {
               $totalEarningFields += $payslipItemRecord[$i]['amount'];
           }
           
           if ($payslipFieldRecord['type'] == self::DEDUCTION_TAX_FIELDS) {
               $totalDeductionTaxFields += $payslipItemRecord[$i]['amount'];
           }
           
           if ($payslipFieldRecord['type'] == self::DEDUCTION_NON_TAX_FIELDS) {
               $totalDeductionNonTaxFields += $payslipItemRecord[$i]['amount'];
           }
        }
        return $totalEarningFields - ($totalDeductionTaxFields + 
                                                $totalDeductionNonTaxFields);
     }
     
     
    /**
     * @param int Indirect Expense ledgerId
     * Creates a row in the Indirect Expense ledger
     * @return int ledger entry ID 
     */
    public function employeeLedgerEntry($data)
    {
        $userProfileModel = new Core_Model_User_Profile($data['employee_id']);
           
        $dataToInsert['debit'] = "0";
        $dataToInsert['credit']= $this->calculateSalaryAmount($data);
        $dataToInsert['notes'] = 'Payslip with Payslip id = '. 
                                                          $this->_payslipId;
        $dataToInsert['transaction_timestamp'] =  $this->_transactionTime;
        $dataToInsert['fa_ledger_id'] = $userProfileModel->getLedgerId();
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param array of items with amount
     * Creates a row in the Indirect Expense ledger
     * @return int ledger entry ID 
     */
    public function salaryLedgerEntry($data)
    {
        unset ($data['employee_id'], $data['date'], $data['ledger_id'], 
                                                              $data['submit']);
        
        $financeLedger = new Core_Model_Finance_Ledger;
        $salesLedgerRecord = $financeLedger->fetchByName('Salaries and Wages');
        
        $totalEarningFields = 0;
        $totalDeductionTaxFields = 0;
        $totalDeductionNonTaxFields = 0;
        
        foreach ($data as $name => $amount) {
           $type = $this->_payslipFieldModel->getTypeByMachineName($name);
           if ($type == self::EARNING_FIELDS) {
               $totalEarningFields += $amount;
               
           }
            
           if ($type == self::DEDUCTION_TAX_FIELDS) {
               $totalDeductionTaxFields += $amount;
           }
           
           if ($type == self::DEDUCTION_NON_TAX_FIELDS) {
               $totalDeductionNonTaxFields += $amount;
           }
        }
        $dataToInsert['debit'] = $totalEarningFields;
        $dataToInsert['credit']= "0";
        $dataToInsert['notes'] = 'Payslip with Payslip id = '. 
                                                          $this->_payslipId;
        $dataToInsert['transaction_timestamp'] =  $this->_transactionTime;
        $dataToInsert['fa_ledger_id'] = $salesLedgerRecord['fa_ledger_id'];
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param int Indirect Expense ledgerId
     * Creates a row in the Indirect Expense ledger
     * @return int ledger entry ID 
     */
    public function taxLedgerEntry($data)
    {
       foreach ($data as $machineName => $amount) {
           $type = $this->_payslipFieldModel->getTypeByMachineName(
                                                                $machineName);
           if ($type == self::DEDUCTION_TAX_FIELDS) {
               if ($amount != '') {
                   $ledgerId = $this->_payslipFieldModel->getLedgerIdByMachineName($machineName);
                   $dataToInsert['debit'] = "0";
                   $dataToInsert['credit']= $amount;
                   $dataToInsert['notes'] = 'Payslip with Payslip id = '. 
                                                          $this->_payslipId;
                   $dataToInsert['transaction_timestamp'] =  
                                                    $this->_transactionTime;
                   $dataToInsert['fa_ledger_id'] = $ledgerId;
                   $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
                   $ledgerEntryIds[] = $ledgerEntryModel->create($dataToInsert);
               }               
           }
       }
      return $ledgerEntryIds;
    }
    
    /**
     * @param ledgerId, amount
     * @return Salary Ledger Entry Id
     */
    protected function advanceLegerEntry($employeeId, $amount) 
    {
        $profileModel = new Core_Model_User_Profile($employeeId);
        $ledgerId = $profileModel->getAdvanceLedgerId();
        $dataToInsert = array(
            'debit' => "0",
            'credit' => $amount,
            'notes' => 'Payslip with Payslip id = '. $this->_payslipId,
            'transaction_timestamp' => time(),
            'fa_ledger_id' => $ledgerId
        );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
        
    /**
     * Fetches a single Payslip record from db 
     * Based on currently set payslip  id
     * @return array of payslip  record 
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('payslip_id = ?', $this->_payslipId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param array $data with keys 
     * updates payslip details and ledger entries
     * @return int
     */
    public function edit($data = array()) 
    {
        $this->_payslipItemModel->deleteAllItemsByPayslipId($this->_payslipId);
        $payslipRecord = $this->fetch();
        $ledgerEntryIds = unserialize($payslipRecord['s_fa_ledger_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $payslipDataToUpdate = array(
            'employee_id' => $data['employee_id'],
            'date' => $date->getTimestamp(),
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId()
        );
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('payslip_id = ?',
                                                           $this->_payslipId);
        $table->update($payslipDataToUpdate, $where);
        
        $this->ledgerEntries($data);
        
        unset ($data['employee_id'], $data['date'], $data['ledger_id'], 
                                                            $data['submit']);
        
        $payslipItemModel = new Core_Model_Finance_Payslip_Item;
        foreach ($data as $machine_name => $amount) {
            $itemData['payslip_field_id'] = 
             $this->_payslipFieldModel->getFieldsIdByMachineName($machine_name);
            $itemData['amount'] = $amount;
            $itemData['payslip_id'] = $this->_payslipId;
            $payslipItemId = $payslipItemModel->create($itemData);
        }
        
        $log = $this->getLoggerService();
        $info = 'Payslip edited with payslip id = '. $this->_payslipId;
        $log->info($info);
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
     * Based on currently set payslip  id
     * @return array of payslip  record 
     */
    public function getItems()
    {
       $itemsRecord = $this->_payslipItemModel->getItemByPayslipId(
                                                           $this->_payslipId);
       for ($i = 0; $i < count($itemsRecord); $i++ ) {
           $fieldName = $this->_payslipFieldModel->getMachineNameById(
                                        $itemsRecord[$i]['payslip_field_id']);
           $result[$fieldName] = $itemsRecord[$i]['amount'];
       }
       return $result;
    }
    
    /** 
     * Based on currently set payslip  id
     * @return array of payslip  record 
     */
    public function getItemsToDisplay()
    {
       $itemsRecord = $this->_payslipItemModel->getItemByPayslipId(
                                                           $this->_payslipId);
       for ($i = 0; $i < count($itemsRecord); $i++ ) {
           $fieldName = $this->_payslipFieldModel->getNameById(
                                        $itemsRecord[$i]['payslip_field_id']);
           $result[$fieldName] = $itemsRecord[$i]['amount'];
       }
       return $result;
    }
    
    /** 
     * Deletes a row in the payslip table
     * Deletes related ledger entries in ledger_entry table
     */
    public function delete()
    {
       $payslipRecord = $this->fetch();
       $ledgerEntryIds = unserialize($payslipRecord['s_fa_ledger_ids']);
       $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
       $table = $this->getTable();
       $where = $table->getAdapter()->quoteInto(
            'payslip_id = ?', $this->_payslipId
       );
       $result = $table->delete($where);
       
       $log = $this->getLoggerService();
       $info = 'Payslip deleted with payslip id = '. $this->_payslipId;
       $log->info($info);
        
       return $result;
    }
    
    /**
     * @return array 
     */
    public function fetchPayslipByDate($date)
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
    

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
class Core_Model_Finance_Ledger extends Core_Model_Abstract
{
    protected $_ledgerId;
    
    const OPENING_BALANCE_TYPE_DEBIT = 1;    
    const OPENING_BALANCE_TYPE_CREDIT = 2;
    const LEDGER_BALANCE_TYPE_DEBIT = 3;
    const LEDGER_BALANCE_TYPE_CREDIT = 4;
    const LEDGER_BALANCE_TYPE_DEBITCREDIT = 5;
        
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_Ledger';


    public function __construct($ledgerId = null)
     {
        if (is_numeric($ledgerId)) {  
            $this->_ledgerId = $ledgerId;
        }
        parent::__construct();
     }
     
    /**
     * @param int $ledgerId
     * @return fluent interface
     */
    public function setLedgerId($ledgerId)
    {
        $this->_ledgerId = $ledgerId;
        return $this;
    }

    /**
     * @return int the ledger ID
     */
    public function getLedgerId()
    {
        return $this->_ledgerId;
    }


    /**
     * Create a finance group record
     * @param array $data with keys 
     * name, fa_group_id, opening_balance_type, opening_balance
     * @return int ledger ID 
     */
    public function create($data = array())
    {
        $table = $this->getTable();

        $dataToInsertLedger = array(
            'name' => $data['name'],
            'fa_group_id' => $data['fa_group_id'],
        );
        
        $this->_ledgerId = $table->insert($dataToInsertLedger);
         
        $dataToInsert = array();

        if ($data['opening_balance_type'] == self::OPENING_BALANCE_TYPE_DEBIT) {
           $dataToInsert['debit'] = $data['opening_balance'];
        } else {
           $dataToInsert['credit'] = $data['opening_balance'];
        }
        
        $dataToInsert['notes'] = 'Opening balance';
        $dataToInsert['transaction_timestamp'] = time();
        $dataToInsert['fa_ledger_id'] = $this->_ledgerId;
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        
        $dataToUpdate['opening_balance_ledger_entry_id'] = $ledgerEntryId;
        $this->edit($dataToUpdate);
         
        $log = $this->getLoggerService();
        $info = 'Ledger created with ledger id = '. $this->_ledgerId;
        $log->info($info);
        return $this->_ledgerId;
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
     * @param string $groupName
     * @return array record
     */
    public function fetchByGroup($groupName)
    {
        $groupModel = new Core_Model_Finance_Group;
        $groupId = $groupModel->getGroupIdByName($groupName);
        $table = $this->getTable();
        $select = $table->select()
                    ->where('fa_group_id = ?', $groupId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return array the ledger record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->join('fa_group',
                            'fa_group.fa_group_id = fa_ledger.fa_group_id',
                            array('fa_group.name as fa_group_name')
                        )
                    ->join('fa_group_category',
                            'fa_group_category.fa_group_category_id = fa_group.fa_group_category_id',
                            array('fa_group_category.name as fa_group_category_name')
                        )
                    ->where('fa_ledger.fa_ledger_id = ?', $this->_ledgerId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    

    /**
     * @param array $data
     * @return bool
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('fa_ledger_id = ?', $this->_ledgerId);
        $result = $table->update($data, $where);
        $log = $this->getLoggerService();
        $info = 'Ledger edited with ledger id = '. $this->_ledgerId;
        $log->info($info);
        return $result;
    }

    /**
     * @return int
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('fa_ledger_id = ?', $this->_ledgerId);
        $log = $this->getLoggerService();
        $info = 'Ledger deleted with ledger id = '. $this->_ledgerId;
        $log->info($info);
        return $table->delete($where);
    }
    
    /**
     * @return int
     */
    public function getLedgerBalanceTypeById()
    {
        $ledgerRecord = $this->fetch();
        $groupName = $ledgerRecord['fa_group_name'];
        $groups = array (
            'Capital Account' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Loans' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Current Liabilities' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Fixed Assets' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Investments' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Current Assets' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Miscellaneous Expenses' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Suspense Account' => self::LEDGER_BALANCE_TYPE_DEBITCREDIT,
            'Sales Accounts' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Purchase Accounts' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Direct Incomes' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Indirect Incomes' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Direct Expenses' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Indirect Expenses' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Reserves & Surplus' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Bank OD Account' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Secured Loans' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Unsecured Loans' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Duties And Taxes' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Provisions' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Sundry Creditors' => self::LEDGER_BALANCE_TYPE_CREDIT,
            'Sundry Debtors' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Stock In Hand' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Deposits' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Loans And Advances' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Cash In Hand' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Bank Accounts' => self::LEDGER_BALANCE_TYPE_DEBIT,
            'Salaries Payable' => self::LEDGER_BALANCE_TYPE_CREDIT,           
        );
        return $groups[$groupName];
    }
    
    /**
     * @return array trial balance
     */
    public function getTrialBalance($ledgerId)
    {
        if ($ledgerId) {
            $this->setLedgerId($ledgerId);
            $ledgerRecord = $this->fetch();
            $ledgerRecords[] = $ledgerRecord;
        }
        else {
            $ledgerRecords = $this->fetchAll();
        }
        
        $result = array();
        for($i = 0; $i <= sizeof($ledgerRecords)-1; $i += 1) {
            $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry();            
            $balance = $ledgerEntryModel->getBalanceByLedgerId(
                                        $ledgerRecords[$i]['fa_ledger_id']);
            $result[$i]['fa_ledger_id'] = $ledgerRecords[$i]['fa_ledger_id'];
            $result[$i]['ledger_name'] = $ledgerRecords[$i]['name'];
            $result[$i]['balance'] = $balance;
        }
      
        $totalDebit = 0;
        $totalCredit = 0;
        for($i = 0; $i <= sizeof($result)-1; $i += 1) {
             $temp['fa_ledger_id'] = $result[$i]['fa_ledger_id'];
             $temp['ledger_name'] = $result[$i]['ledger_name'];
             $temp['balance'] = $result[$i]['balance'];
             $this->setLedgerId($result[$i]['fa_ledger_id']);
             $temp['balance_type'] = $this->getLedgerBalanceTypeById();
             $trialBalance[$i] = $temp;
        }
        return $trialBalance;
    }
    
    /**
     * @return array summary
     */
    public function getSummary($ledgerId)
    {
       if ($ledgerId) {
            $this->setLedgerId($ledgerId);
            $ledgerRecord = $this->fetch();
            $ledgerRecords[] = $ledgerRecord;
        }
        else {
            $ledgerRecords = $this->fetchAll();
        }
       $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
              
       for($i = 0; $i <= sizeof($ledgerRecords)-1; $i += 1) {
           $temp['fa_ledger_id'] = $ledgerRecords[$i]['fa_ledger_id'];
           $temp['ledger_name'] = $ledgerRecords[$i]['name'];
           $temp['balance'] = $ledgerEntryModel->getBalanceByLedgerId(
                                           $ledgerRecords[$i]['fa_ledger_id']);
           $summary[] = $temp;
        }
        return $summary;
    }
    
    /**
     * @param group name
     * @return array - ledger name as key and balance as value
     */
    public function getLedgerBalanceByGroup($groupName)
    {
        $ledgerRecords = $this->fetchByGroup($groupName);
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        for($i = 0; $i <= sizeof($ledgerRecords)-1; $i += 1) {
           $ledgerName = $ledgerRecords[$i]['name'];
           $result[$ledgerName] = $ledgerEntryModel->getBalanceByLedgerId(
                                  $ledgerRecords[$i]['fa_ledger_id']);
        }
        
       return $result;
    }
    
    /**
     * @return int opening balance ledger entry id
     */
    public function getOpeningBalanceId()
    {
        $ledgerRecord = $this->fetch();
        return $ledgerRecord['opening_balance_ledger_entry_id'];
    }
    
    /**
     * @return array the ledger entry record
     */
    public function editOpeningBalance($data)
    {
        $ledgerEntryId = $this->getOpeningBalanceId();
               
        if ($data['opening_balance_type'] == self::OPENING_BALANCE_TYPE_DEBIT) {
           $dataToUpdate['debit'] = $data['opening_balance'];
           $dataToUpdate['credit'] = 0;
        } else {
           $dataToUpdate['credit'] = $data['opening_balance'];
           $dataToUpdate['debit'] = 0;
        }
        
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryModel->setLedgerEntryId($ledgerEntryId);
        $ledgerEntryModel->edit($dataToUpdate);
        return $result;
    }

    /**
     * @return string PDF file location 
     */
    public function getPdfFileLocation()
    {
        $summaryDetails = $this->getSummary();

        $pdf = new Core_Model_Finance_Ledger_Pdf_Create();
        $pdf->setSummaryDetails($summaryDetails);
        $pdf->run();
        $pdfPath = APPLICATION_PATH 
                     . '/data/documents/reports/ledger_wise_summary' . '.pdf';
        $pdf->Output($pdfPath, 'F');
        return $pdfPath;
    }

    /**
     * @return string PDF file location 
     */
    public function getTrialBalancePdfFileLocation($ledgerId)
    {
        $trialBalanceDetails = $this->getTrialBalance($ledgerId);

        $pdf = new Core_Model_Finance_Ledger_Pdf_TrialBalance();
        $pdf->setSummaryDetails($trialBalanceDetails);
        $pdf->run();
        $pdfPath = APPLICATION_PATH
                     . '/data/documents/reports/trial_balance_summary' . '.pdf';
        $pdf->Output($pdfPath, 'F');
        return $pdfPath;
    }
    
    /**
     * @param array data with keys closing_date
     */
    public function closeAccounts($data)
    {
        $date = $data['closing_date'];
        $ledgersRecord = $this->fetchAll();
        for($i = 0; $i <= sizeof($ledgersRecord)-1; $i += 1) {
            $this->_ledgerId = $ledgersRecord[$i]['fa_ledger_id'];
            $ledgerEntryModel =  new Core_Model_Finance_Ledger_Entry;
            $ledgerEntries = $ledgerEntryModel->fetchEntriesForClosing(
                                            $date, $this->_ledgerId);
            
            $balance = 0;
            for($l = 0; $l <= sizeof($ledgerEntries)-1; $l += 1) {
                $currentBalance = 
                   $ledgerEntries[$l]['debit'] - $ledgerEntries[$l]['credit'];
                $balance = $balance - $currentBalance;           
            }
            
            if ($balance > 0) {
                $dataToLedgerEntry['debit'] = 0;
                $dataToLedgerEntry['credit'] = $balance;
            }
            else {
                $dataToLedgerEntry['debit'] = abs($balance);
                $dataToLedgerEntry['credit'] = 0;
            }
            $dataToLedgerEntry['fa_ledger_id'] = $this->_ledgerId;
            $dataToLedgerEntry['notes'] = "Opening balance";
            $dataToLedgerEntry['transaction_timestamp'] = time();
                       
            $ledgerEntryId = $ledgerEntryModel->create($dataToLedgerEntry);
            
            $dataToUpdate['opening_balance_ledger_entry_id'] = $ledgerEntryId;
            $this->edit($dataToUpdate);   
        }
    }
        
}



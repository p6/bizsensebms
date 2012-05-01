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
class Core_Model_SalesReturn extends Core_Model_Abstract
{
    /**
     * @var the  SalesReturn ID
     */
	protected $_salesReturnId;
	    
    /**
     * @param $salesReturnId
     */
    public function __construct($salesReturnId = null)
    {
        if (is_numeric($salesReturnId)) {  
            $this->_salesReturnId = $salesReturnId;
        }
        parent::__construct();
    }
     
    /**
     * @var object the Sales Return item model
     */
    protected $_itemModel;

    /**
     * @var object the ledger entry model
     */
    protected $_ledgerEntryModel;
    
	 /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_SalesReturn';

    /**
     * @var to store invoice to 
     */
    protected $_invoiceTo;

    /**
     * @var to store date for transaction
     */
    protected $_transactionTime;
    
	/**
     * @param int $salesReturnId
     * @return fluent interface
     */
    public function setSalesReturnId($salesReturnId)
    {
        $this->_salesReturnId = $salesReturnId;
        return $this;
    }

    /**
     * @return int the Sales Return ID
     */
    public function getSalesReturnId()
    {
        return $this->_salesReturnId;
    }
    
    /**
     * Create a Sales Return
     * @param array $data with keys 
     * @return int Sales Return ID 
     */
    public function create($itemsData = array(), $data = array(), $invoiceId)
    {
        $date = new Zend_Date($data['date']);

        $salesReturnData = array(
            'invoice_id' => $invoiceId,
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'customer_sales_return_reference' => 
                                        $data['customer_sales_return_reference']
        );
        
        $this->_salesReturnId = parent::create($salesReturnData);
        $this->_transactionTime = $date->getTimestamp();
        
        $salesReturnItemModel = new Core_Model_SalesReturn_Item;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $salesReturnItemId = $salesReturnItemModel->create($itemsData[$i], 
                                                         $this->_salesReturnId);
        }
        
        $invoiceModel = new Core_Model_Invoice($invoiceId);
        $invoiceRecord = $invoiceModel->fetch();
        
        if ($invoiceRecord['to_type'] == Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            $accountModel =  new Core_Model_Account($invoiceRecord['to_type_id']);
            $this->_invoiceTo = 'Type = Account Name = '.
                                                $accountModel->getName();
            $this->ledgerEntries($accountModel->getLedgerId());
        }
        
        if ($invoiceRecord['to_type'] == Core_Model_Invoice::TO_TYPE_CONTACT) {
            $contactModel =  new Core_Model_Contact($invoiceRecord['to_type_id']);
            $this->_invoiceTo = 'Type = Contact  Name = '.
                                                $contactModel->getFullName();
            $this->ledgerEntries($contactModel->getLedgerId());
        }
        
        return $this->_salesReturnId;
    }
    
    /**
     * @param ledger id
     * invoice ledger entries and update ledger entry ids to invoice 
     * table(s_ledger_entry_ids)
     * @return bool
     */
    public function ledgerEntries($ledgerId)
    {
        $fa_ledger_entry_ids = array(
                        '0' => $this->customerLedgerEntry($ledgerId),
                        '1' => $this->salesAccountLedgerEntry()
                        );
        $salesReturnRecord = $this->fetch();
        $invoiceModel = new Core_Model_Invoice($salesReturnRecord['invoice_id']);
        $invoiceRecord = $invoiceModel->fetch();
        if ($invoiceRecord['discount_amount'] != 0 || 
                                $invoiceRecord['discount_amount'] != null) {
            $ledgerEntryId = $this->discountLedgerEntry(
                                        $invoiceRecord['discount_amount']);
            array_push($fa_ledger_entry_ids, $ledgerEntryId);
        }
            
        if ($invoiceRecord['freight_amount'] != 0 || 
                                $invoiceRecord['freight_amount'] != null) {
            $ledgerEntryId = $this->freightLedgerEntry(
                                        $invoiceRecord['freight_amount']);
            array_push($fa_ledger_entry_ids, $ledgerEntryId);
        }
        $taxLedgerIds = $this->taxLedgerEntry();
        if ($taxLedgerIds != '') {
            $taxLedgerEntries = $taxLedgerIds;
        }
        for($i = 0; $i <= sizeof($taxLedgerEntries)-1; $i += 1) {
            array_push($fa_ledger_entry_ids, $taxLedgerEntries[$i]);
        }
        $dataToUpdate['s_ledger_entry_ids'] = serialize($fa_ledger_entry_ids);
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('sales_return_id = ?', 
                                                         $this->_salesReturnId);
        $result = $table->update($dataToUpdate, $where);
        
        return $result;
    }
    
    /**
     * Creates a row in the customer ledger
     * @return ledger entry id
     */
    public function customerLedgerEntry($ledgerId)
    {
        $notes = 'Sales Return with Sales Return Id = '.$this->_salesReturnId.
                 '  '.$this->_invoiceTo;
        $dataToInsert = array(
             'debit' => "0",
             'credit' =>  $this->getTotalAmount(),
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $ledgerId
           );
       $ledgerEntryId = $this->getLedgerEntryModel()->create($dataToInsert);
       return $ledgerEntryId;
    }
    
    /**
     * Creates a row in the discount ledger
     * @param float amount
     * @return ledger entry id
     */
    public function discountLedgerEntry($amount)
    {
        $financeLedger = new Core_Model_Finance_Ledger;
        $discountLedgerRecord = $financeLedger->fetchByName('Discount allowed');
        $notes = 'Invoice with Invoice Id = '.$this->_invoiceId.
                 '  '.$this->_invoiceTo;
        $dataToInsert = array(
             'debit' => "0",
             'credit' => $amount,
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $discountLedgerRecord['fa_ledger_id']
           );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * Creates a row in the discount ledger
     * @param float amount
     * @return ledger entry id
     */
    public function freightLedgerEntry($amount)
    {
        $financeLedger = new Core_Model_Finance_Ledger;
        $freightLedgerRecord = $financeLedger->fetchByName('Freight Inward');
        $notes = 'Invoice with Invoice Id = '.$this->_invoiceId.
                 '  '.$this->_invoiceTo;
        $dataToInsert = array(
             'debit' => $amount,
             'credit' => "0",
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $freightLedgerRecord['fa_ledger_id']
           );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
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
     * Creates a row in the sales account ledger
     * calculate total sales price by items
     * @return ledger entry id
     */
    public function salesAccountLedgerEntry()
    {
        $items = $this->getItems();
        $financeLedger = new Core_Model_Finance_Ledger;
        $salesLedgerRecord = $financeLedger->fetchByName('Sales Account');
        
        $totalPrice = 0;
        for($i = 0; $i <= sizeof($items)-1; $i += 1) {
             $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
             $totalPrice = $totalPrice + $price;
        }
        $notes = 'Sales Return with Sales Return Id = '.$this->_salesReturnId.
                 '  '.$this->_invoiceTo;
        $dataToInsert = array(
             'debit' => $totalPrice,
             'credit' => "0",
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $salesLedgerRecord['fa_ledger_id']
           );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * Creates a row in the tax ledger
     * Calculate total amount of tax for each tax type and credit amount to 
     * repective tax ledger 
     * @return array of ledger ids
     */
    public function taxLedgerEntry()
    {
        $items = $this->getItems();
        $totalPrice = 0;
        $tax_types = array ();
        $ledgerEntryIds = array ();
        $taxTypeModel = new Core_Model_Tax_Type;
        for($i = 0; $i <= sizeof($items)-1; $i += 1) {
           if($items[$i]['tax_type_id'] != '0') {
                $taxName = 
                     $taxTypeModel->getTaxNameFromId($items[$i]['tax_type_id']);
                if (!(in_array($taxName, $tax_types))) { 
                    array_push($tax_types, $taxName);
                }
            }
        }
        
        for ($tax = 0; $tax <= sizeof($tax_types)-1; $tax += 1) {
            $totalTaxAmount = 0;
            for($i = 0; $i <= sizeof($items)-1; $i += 1) {
                $taxName = 
                     $taxTypeModel->getTaxNameFromId($items[$i]['tax_type_id']);
                if($taxName == $tax_types[$tax])
                {
                   $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
                   $taxPercentage = $taxTypeModel->getTaxPercentageFromId($items[$i]['tax_type_id']);
                   $taxAmount = ($price * $taxPercentage) /  100 ;
                   $totalTaxAmount = $totalTaxAmount + $taxAmount;                   
                }
            }
            $financeLedger = new Core_Model_Finance_Ledger;
            $salesLedgerRecord = $financeLedger->fetchByName($tax_types[$tax]);
            $notes = 'Sales Return with Sales Return Id = '.
                     $this->_salesReturnId.'  '.$this->_invoiceTo;
            $dataToInsert = array(
                'debit' => $totalTaxAmount,
                'credit' => "0",
                'notes' => $notes,
                'transaction_timestamp' => $this->_transactionTime,
                'fa_ledger_id' => $salesLedgerRecord['fa_ledger_id']
                );
            $ledgerEntryId = $this->getLedgerEntryModel()->create($dataToInsert);
            array_push($ledgerEntryIds, $ledgerEntryId);
           }
         return $ledgerEntryIds;
      }
      
    /**
     * @return float the salesreturn total amount
     */
    public function getTotalAmount()
    {
        $invoiceItems = $this->getItems();
        $total = 0;
        foreach ($invoiceItems as $item) {
            $beforeTaxLineTotal = $item['unit_price'] * $item['quantity'];
            $taxPercentage = 0;
            if (is_numeric($item['tax_type_id'])) {
                $taxTypeModel = new Core_Model_Tax_Type($item['tax_type_id']);
                $taxPercentage = $taxTypeModel->getPercentage();
            }
            $taxAmount =  ($beforeTaxLineTotal * $taxPercentage) /  100 ;
            $afterTaxLineTotal = $beforeTaxLineTotal + $taxAmount;
            $total += $afterTaxLineTotal; 
        }
        $salesReturnRecord = $this->fetch();
        $invoiceModel = new Core_Model_Invoice($salesReturnRecord['invoice_id']);
        $invoiceRecord = $invoiceModel->fetch();
        
        if ($invoiceRecord['discount_amount'] != 0 || 
                                $invoiceRecord['discount_amount'] != null) {
            $total -= $invoiceRecord['discount_amount'];
        }

        if ($invoiceRecord['freight_amount'] != 0 || 
                                $invoiceRecord['freight_amount'] != null) {
            $total += $invoiceRecord['freight_amount'];
        }
        return $total;
    }
    
    /**
     * @return array the salesReturn items
     */
    public function getItems()
    {
        $table = $this->getItemModel()->getTable();
        $select = $table->select();
        $select->where('sales_return_id = ?', $this->_salesReturnId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
       return $result;
    }
    
    /**
     * @return object Core_Model_salesReturn_Item 
     */
    public function getItemModel()
    {
        if (null === $this->_itemModel) {
            $this->_itemModel = new Core_Model_SalesReturn_Item;
        }
        return $this->_itemModel;
    }
    
    /**
     * @return json Sales return items
     */
    public function getItemsJson()
    {
        $items = $this->getItems();
        $itemToReturn = array();
        foreach ($items as $item) {
            $temp = $item;
            //$temp['product_id'] = $temp['product_id'];
            $itemToReturn[] = $temp;
        }
        return $itemToReturn;
    }
    
    /**
     * Fetches a single Sales Return record from db 
     * Based on currently set sales return Id
     * @return array of Sales Return record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()->where(
            'sales_return_id = ?', $this->_salesReturnId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
    * Edit the Sales Return
    * @param array $data with keys
    * @param array $itemsData the invoice items that have changed
    * @return bool
    */ 
    public function edit($itemsData = array(), $data = array()) 
    {  
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $returnLedgerRecord = $this->fetch();
        $ledgerEntryIds = unserialize($returnLedgerRecord['s_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $invoiceModel = new Core_Model_Invoice($returnLedgerRecord['invoice_id']);
        $invoiceRecord = $invoiceModel->fetch();
        
        if ($invoiceRecord['to_type'] == Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            $accountModel =  new Core_Model_Account($invoiceRecord['to_type_id']);
             $this->_invoiceTo = 'Type = Account  Name = '.
                                                $accountModel->getName();
            $this->ledgerEntries($accountModel->getLedgerId());
        }
        
        if ($invoiceRecord['to_type'] == Core_Model_Invoice::TO_TYPE_CONTACT) {
            $contactModel =  new Core_Model_Contact($invoiceRecord['to_type_id']);
            $this->_invoiceTo = 'Type = Contact  Name = '.
                                                $contactModel->getFullName();
            $this->ledgerEntries($contactModel->getLedgerId());
        }
        
        $table = $this->getTable();
                
        $dataToUpdate = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'customer_sales_return_reference' => 
                                        $data['customer_sales_return_reference']
        );

        $where = $table->getAdapter()->quoteInto('sales_return_id = ?', $this->_salesReturnId);
        $result = $table->update($dataToUpdate, $where);

        $res = $this->getItemModel()->setSalesReturnModel($this)->deleteAll();
        
        $salesReturnItemModel = new Core_Model_SalesReturn_Item;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $salesReturnItemId = $salesReturnItemModel->create($itemsData[$i], 
                                                       $this->_salesReturnId);
        }
        
        return $result;
        
    }    
    
    /**
     * @return string PDF file location 
     */
    public function getPdfFileLocation()
    {    
        $salesReturnLedgerRecord = $this->fetch();
        $invoiceModel = new Core_Model_Invoice($salesReturnLedgerRecord['invoice_id']);
        
        $pdf = new Core_Model_SalesReturn_Pdf();
        $pdf->setModel($this);
        $pdf->setInvoiceModel($invoiceModel);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/documents/salesreturn/salesreturn_' . 
                                                $this->_salesReturnId . '.pdf';
        $pdf->Output($pdfPath, 'F');
        return $pdfPath; 
    }
    
    /** 
     * Deletes a row in the Sales Return table
     * Deletes related ledger entries in ledger_entry table
     * @return bool
     */
    public function delete()
    {       
        $returnLedgerRecord = $this->fetch();
        $ledgerEntryIds = unserialize($returnLedgerRecord['s_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto(
            'sales_return_id = ?', $this->_salesReturnId
        );
        $result = $table->delete($where);
        
        return $result;
    }
    
    /** 
     * @param invoice id
     * Deletes a row in the Sales Return table
     * Deletes related ledger entries in ledger_entry table
     * @return bool
     */
    public function deleteByInvoiceId($invoiceId)
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('invoice_id = ?', $invoiceId);
        $select = $table->select()->where($where);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        
        for ($i = 0; $i < count($result); $i++ ) {
           $ledgerEntryIds = unserialize($result[$i]['s_ledger_entry_ids']);
           $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        }  
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('invoice_id = ?', $invoiceId);
        $result = $table->delete($where);
        return $result;
    }
    
    /**
     * @return Sales Return Register
     */
    public function salesReturnRegister()
    {
        $salesReturnDetails = $this->fetchAll();
        
         $no_value = array();
        if (!$salesReturnDetails) {
            return $no_value;
        }
        
        for($sr = 0; $sr <= sizeof($salesReturnDetails)-1; $sr += 1) {
         /**
          * date
          */
        $date = new Zend_Date();
        $date->setTimestamp($salesReturnDetails[$sr]['date']);
        $temp['date'] = $date->get(Zend_Date::DATE_MEDIUM);
            
        /**
         * party name
         */
        $invoiceModel = new Core_Model_Invoice(
                        $salesReturnDetails[$sr]['invoice_id']);
        $invoiceRecord = $invoiceModel->fetch();
        if ($invoiceRecord['to_type'] == 
                                    Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            $accountModel = new Core_Model_Account(
                               $invoiceRecord['to_type_id']);
            $partyName = $accountModel->getName();
        } 
        else {
            $contactModel = new Core_Model_Contact(
            $invoiceRecord['to_type_id']);
            $partyName = $contactModel->getFullName();
        }
        $temp['particulars'] = $partyName;
            
        /**
         * Type
         */
         $temp['type'] = "Sales Return";
            
         /**
          * Invoice Id
          */
          $temp['id'] = $salesReturnDetails[$sr]['sales_return_id'];
           
         /**
          * Invoice Tax
          */
          $this->setSalesReturnId($salesReturnDetails[$sr]['sales_return_id']);
          
          $temp['total_amount'] = $this->getTotalAmount();
          $items = $this->getItems();
          $totalPrice = 0;
          $tax_types = array ();
          $ledgerEntryIds = array ();
            
          $taxTypeModel = new Core_Model_Tax_Type;
            for($i = 0; $i <= sizeof($items)-1; $i += 1) {
                if($items[$i]['tax_type_id'] != '0') {
                $taxName = 
                     $taxTypeModel->getTaxNameFromId($items[$i]['tax_type_id']);
                    if (!(in_array($taxName, $tax_types))) { 
                    array_push($tax_types, $taxName);
                    }
                }
            }  
            
          for ($tax = 0; $tax <= sizeof($tax_types)-1; $tax += 1) {
            $totalTaxAmount = 0;
                for($i = 0; $i <= sizeof($items)-1; $i += 1) {
                $taxName = 
                     $taxTypeModel->getTaxNameFromId($items[$i]['tax_type_id']);
                    if($taxName == $tax_types[$tax]) {
                   $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
                   $taxPercentage = $taxTypeModel->getTaxPercentageFromId(
                                                     $items[$i]['tax_type_id']);
                   $taxAmount = ($price * $taxPercentage) /  100 ;
                   $totalTaxAmount = $totalTaxAmount + $taxAmount;                   
                   }
                }
                $taxType = $tax_types[$tax];
                $invoiceTax[$taxType] = $totalTaxAmount;
           }// end of tax loop
              $temp['tax'] = $invoiceTax;
              $salesRegister[] = $temp;
             
       }// end of invoice loop
       return $salesRegister;
    }
    
    /**
     * @param $date
     * @return array of invoice data
     */
    public function fetchSalesReturnByDate($date)
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

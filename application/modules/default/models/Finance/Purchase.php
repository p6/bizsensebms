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
class Core_Model_Finance_Purchase extends Core_Model_Abstract
{
    /**
     * @var the purchase ID
     */
     protected $_purchaseId;
    
    /**
     * The purchase from - account or contact
     */
     const TYPE_PURCHASE = 1;
     const TYPE_PURCHASE_VOUCHER_ASSEST = 2;
     const TYPE_PURCHASE_VOUCHER_EXPENSE = 3;
     
    /**
     * @var to store date for transaction
     */
    protected $_transactionTime;
    
    /**
     * @param purchaseId
     */
     public function __construct($purchaseId = null)
     {
        if (is_numeric($purchaseId)) {  
            $this->_purchaseId = $purchaseId;
        }
        parent::__construct();
     }
     
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
     protected $_dbTableClass = 'Core_Model_DbTable_Finance_Purchase';
     
    /**
     * @var object the purchase item model
     */
    protected $_itemModel;
    
    /**
     * @var object the purchase item model
     */
    protected $_voucherItemModel;

    /**
     * @param int $purchaseId
     * @return fluent interface
     */
    public function setPurchaseId($purchaseId)
    {
        $this->_purchaseId = $purchaseId;
        return $this;
    }

    /**
     * @return int the Bank account ID
     */
    public function getPurchaseId()
    {
        return $this->_purchaseId;
    }


    /**
     * Create a finance Purchase
     * @param array $itemsData with keys and $data with keys 
     * @return int ledger ID 
     */
    public function create($itemsData,$data)
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $purchaseData = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'vendor_id' => $data['vendor_id'],
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'payment_terms' => $data['payment_terms'],
            'freight_amount' => $data['freight_amount'],
            'discount_amount' => $data['discount_amount'],
            'type' => self::TYPE_PURCHASE
        );
        
        $this->_purchaseId = parent::create($purchaseData);
        
        $purchaseItemModel = new Core_Model_Finance_Purchase_Item;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $purchaseItemId = $purchaseItemModel->create($itemsData[$i], 
                                                            $this->_purchaseId);
        }
        
        $vendorModel = new Core_Model_Finance_Vendor($data['vendor_id']);
        $vendorLedgerId = $vendorModel->getLedgerId();
        $vendorName = $vendorModel->getName();
        $notes = 'Purchase from '.$vendorName;
        $vendorLedgerEntryId = $this->vendorLedgerEntry($vendorLedgerId, 
                                                                       $notes);
        $taxLedgerIds = $this->taxLedgerEntry($notes);
        if ($taxLedgerIds != '') {
            $taxLedgerEntries = $taxLedgerIds;
        }
        $fa_ledger_entry_ids = array();
        $fa_ledger_entry_ids['0'] = $vendorLedgerEntryId;
        $fa_ledger_entry_ids['1'] = $this->purchaseAccountLedgerEntry();
        
        if ($data['discount_amount'] != 0 || $data['discount_amount'] != null) {
            $ledgerEntryId = $this->discountLedgerEntry(
                                        $data['discount_amount'], $notes);
            array_push($fa_ledger_entry_ids, $ledgerEntryId);
        }
        
        if ($data['freight_amount'] != 0 || 
                                $data['freight_amount'] != null) {
            $ledgerEntryId = $this->freightLedgerEntry(
                                        $data['freight_amount'], $notes);
            array_push($fa_ledger_entry_ids, $ledgerEntryId);
        }
        
        for($i = 0; $i <= sizeof($taxLedgerEntries)-1; $i += 1) {
                array_push($fa_ledger_entry_ids, $taxLedgerEntries[$i]);
        }
        $fa_ledger_entry_ids = serialize($fa_ledger_entry_ids);
        $table = $this->getTable();
        $dataToUpdate['s_ledger_ids'] = $fa_ledger_entry_ids;
        $where = $table->getAdapter()->quoteInto('purchase_id = ?', 
                                                            $this->_purchaseId);
        $table->update($dataToUpdate, $where);
        
        $log = $this->getLoggerService();
        $info = 'Purchase created with purchas id = '. $this->_purchaseId;
        $log->info($info);
        
        return $this->_purchaseId;
    }
    
    /**
     * Creates a row in the freight
     * @param float amount
     * @return ledger entry id
     */
    public function freightLedgerEntry($amount, $notes)
    {
        $financeLedger = new Core_Model_Finance_Ledger;
        $freightLedgerRecord = $financeLedger->fetchByName('Freight Outward');

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
     * Creates a row in the purchase account ledger
     * calculate total purchase price by items
     * return ledger entry id
     */
    public function purchaseAccountLedgerEntry()
    {
        $items = $this->getItems();
               
        $financeLedger = new Core_Model_Finance_Ledger;
        $salesLedgerRecord = $financeLedger->fetchByName('Purchase Account');
        
        $totalPrice = 0;
        for($i = 0; $i <= sizeof($items)-1; $i += 1) {
             $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
             $totalPrice = $totalPrice + $price;
        }
        $notes = 'Purchase with Purchase Id = '.$this->_purchaseId;
                 
        $dataToInsert = array(
             'debit' => $totalPrice,
             'credit' =>  "0",
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $salesLedgerRecord['fa_ledger_id']
           );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param ledgerId
     * Creates a row in the vendor ledger
     * @return int ledger entry ID 
     */
    public function vendorLedgerEntry($ledgerId, $notes)
    {
        $dataToInsert['debit'] = "0";
        $dataToInsert['credit']= $this->getTotalAmount();
        $dataToInsert['notes'] = $notes;
        $dataToInsert['transaction_timestamp'] = $this->_transactionTime;
        $dataToInsert['fa_ledger_id'] = $ledgerId;
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * Creates a row in the tax ledger
     * @return int ledger entry ID
     */
    public function taxLedgerEntry($notes)
    {
        $items = $this->getItems();
        $totalPrice = 0;
        $tax_types = array ();
        $ledgerEntryIds = array ();
        $taxTypeModel = new Core_Model_Tax_Type;
        
        for($i = 0; $i <= sizeof($items)-1; $i += 1) {
           if($items[$i]['tax_type_id'] != null) {
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
                $taxName = $taxTypeModel->getTaxNameFromId(
                                                     $items[$i]['tax_type_id']);
                if ($taxName == $tax_types[$tax]) {
                    $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
                    $taxPercentage = $taxTypeModel->getTaxPercentageFromId(
                                                    $items[$i]['tax_type_id']);
                    $taxAmount =  ($price * $taxPercentage) /  100 ;
                    $totalTaxAmount = $totalTaxAmount + $taxAmount;                   
                }
            }
            $financeLedger = new Core_Model_Finance_Ledger;
            $salesLedgerRecord = $financeLedger->fetchByName($tax_types[$tax]);
            $dataToInsert['debit'] = $totalTaxAmount;
            $dataToInsert['credit']= "0";     
            $dataToInsert['notes'] = $notes;
            $dataToInsert['transaction_timestamp'] = $this->_transactionTime;
            $dataToInsert['fa_ledger_id'] = $salesLedgerRecord['fa_ledger_id'];
            $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
            $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
            array_push($ledgerEntryIds, $ledgerEntryId);
           }
           return $ledgerEntryIds;
    }
    
    /**
     * Creates a row in the discount ledger
     * @param float amount
     * @return ledger entry id
     */
    public function discountLedgerEntry($amount, $notes)
    {
        $financeLedger = new Core_Model_Finance_Ledger;
        $discountLedgerRecord = $financeLedger->fetchByName('Discount received');
        
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
     * @return Ledger Id
     */
    public function getLedgerId()
    {
       $data = $this->fetch();
       return $data->fa_ledger_id;
    }
    
    /**
     * Fetches a single Purchase record from db 
     * Based on currently set purchaseId
     * @return array of Purchase record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('purchase_id = ?', $this->_purchaseId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param array $itemsData with keys and $data with keys 
     * updates bankaccount details and ledger entries
     * @return int
     */
    public function edit($itemsData = array(), $data = array()) 
    {
        $this->prepareEphemeral();    
        $this->_metaData = $data;
        $this->_itemsData = $itemsData;
        $table = $this->getTable();
        
        $date = new Zend_Date($this->_metaData['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $dataToUpdate = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'vendor_id' => $data['vendor_id'],
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'payment_terms' => $data['payment_terms'],
            'freight_amount' => $data['freight_amount'],
            'discount_amount' => $data['discount_amount'],
            'type' => self::TYPE_PURCHASE
        ); 
        
        $where = $table->getAdapter()->quoteInto('purchase_id = ?',
                                                            $this->_purchaseId);
        $table->update($dataToUpdate, $where);
        
        $this->getItemModel()->setPurchaseModel($this)->deleteAll();
        
        $purchaseItemModel = new Core_Model_Finance_Purchase_Item;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $purchaseItemId = $purchaseItemModel->create($itemsData[$i],
                                                            $this->_purchaseId);
        }
        $LedgerEntyModel = new Core_Model_Finance_Ledger_Entry;
        $purchaseRecord = $this->fetch();
        $ledgerEntryIds = unserialize($purchaseRecord['s_ledger_ids']);
        
        for($i = 0; $i <= sizeof($ledgerEntryIds)-1; $i += 1) {
            $result = $LedgerEntyModel->deleteById($ledgerEntryIds[$i]);
        }
        
        $vendorModel = new Core_Model_Finance_Vendor($data['vendor_id']);
        $vendorLedgerId = $vendorModel->getLedgerId();
        $vendorName = $vendorModel->getName();
        $notes = 'Purchase from '.$vendorName;
        $vendorLedgerEntryId = $this->vendorLedgerEntry($vendorLedgerId, $notes);
        $taxLedgerEntries = $this->taxLedgerEntry($notes);
        
        $fa_ledger_entry_ids = array();
        $fa_ledger_entry_ids['0'] = $vendorLedgerEntryId;
        $fa_ledger_entry_ids['1'] = $this->purchaseAccountLedgerEntry();
        if ($data['discount_amount'] != 0 || $data['discount_amount'] != null) {
            $ledgerEntryId = $this->discountLedgerEntry(
                                        $data['discount_amount'], $notes);
            array_push($fa_ledger_entry_ids, $ledgerEntryId);
        }
        for($i = 0; $i <= sizeof($taxLedgerEntries)-1; $i += 1) {
                array_push($fa_ledger_entry_ids, $taxLedgerEntries[$i]);
        }
        $fa_ledger_entry_ids = serialize($fa_ledger_entry_ids);
        $table = $this->getTable();
        $dataToUpdate['s_ledger_ids'] = $fa_ledger_entry_ids;
        $where = $table->getAdapter()->quoteInto('purchase_id = ?',
                                                            $this->_purchaseId);
        $result = $table->update($dataToUpdate, $where);
        
        $log = $this->getLoggerService();
        $info = 'Purchase edited with purchas id = '. $this->_purchaseId;
        $log->info($info);
        
        return $result;
    }
    
     /**
     * deletes a row in table based on currently set paymentId
     * deletes respective ledger entry details
     * @return int
     */
    public function delete()
    {
        $LedgerEntyModel = new Core_Model_Finance_Ledger_Entry;
        $purchaseRecord = $this->fetch();
        $ledgerEntryIds = unserialize($purchaseRecord['s_ledger_ids']);
        for($i = 0; $i <= sizeof($ledgerEntryIds)-1; $i += 1) {
            $result = $LedgerEntyModel->deleteById($ledgerEntryIds[$i]);
        }
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto(
            'purchase_id = ?', $this->_purchaseId
        );
        $result = $table->delete($where);
        
        $log = $this->getLoggerService();
        $info = 'Purchase deleted with purchas id = '. $this->_purchaseId;
        $log->info($info);
        
        return $result;
    }
    
    /**
     * @return array the purchase items
     */
    public function getPurchaseItems()
    {
        $purchaseRecord = $this->fetch();
        
        return $purchaseRecord; 
    }
    
    /**
     * @return array the json purchase items
     */
    public function getItemsJson()
    {
        $items = $this->getItems();
        
        $itemToReturn = array();
        foreach ($items as $item) {
            $temp = $item;
            //$temp['product_id'] = $temp['invoice_item_inventory_id'];
            $itemToReturn[] = $temp;
        }
        return $itemToReturn;
    }
    
    /**
     * @return array the json purchase items
     */
    public function getVoucherItemsJson()
    {
        $items = $this->getVoucherItems();
        
        $itemToReturn = array();
        foreach ($items as $item) {
            $temp = $item;
            //$temp['product_id'] = $temp['invoice_item_inventory_id'];
            $itemToReturn[] = $temp;
        }
        return $itemToReturn;
    }
    
    /**
     * @return float the purchase total amount
     */
    public function getTotalAmount()
    {
        $purchaseRecord = $this->fetch();
        if ($purchaseRecord['type'] == 1) {
            $purchaseItems = $this->getItems();
        }
        else {
            $purchaseItems = $this->getVoucherItems();
        }
        $total = 0;
        foreach ($purchaseItems as $item) {
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
        
        if ($purchaseRecord['discount_amount'] != 0 || 
                                $purchaseRecord['discount_amount'] != null) {
            $total -= $purchaseRecord['discount_amount'];
        }
        if ($purchaseRecord['freight_amount'] != 0 || 
                                $purchaseRecord['freight_amount'] != null) {
            $total += $purchaseRecord['freight_amount'];
        }
        return $total;
    }
    
    /**
     * @return object Core_Model_Finance_Purchase_Item the 
     * dependant invoice item model
     */
    public function getItemModel()
    {
        if (null === $this->_itemModel) {
            $this->_itemModel = new Core_Model_Finance_Purchase_Item;
        }
        return $this->_itemModel;
    }
    
    /**
     * @return object Core_Model_Finance_Purchase_ItemOthers the 
     * dependant invoice item model
     */
    public function getVoucherItemModel()
    {
        if (null === $this->_voucherItemModel) {
            $this->_voucherItemModel = new Core_Model_Finance_Purchase_ItemOthers;
        }
        return $this->_voucherItemModel;
    }
    
    /**
     * @return array the purchase items
     */
    public function getItems()
    {
        $table = $this->getItemModel()->getTable();
        $select = $table->select();
        $select->where('purhcase_id = ?', $this->_purchaseId);
        $result = $table->fetchAll($select);
        if ($result) {
                $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return array the purchase items
     */
    public function getVoucherItems()
    {
        $table = $this->getVoucherItemModel()->getTable();
        $select = $table->select();
        
        $select->where('purchase_id = ?', $this->_purchaseId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return Sales Register
     */
    public function purchaseRegister()
    {
        $purchaseDetails = $this->fetchAll();
        
        $no_value = array();
        if (!$purchaseDetails) {
            return $no_value;
        }
        for($p = 0; $p <= sizeof($purchaseDetails)-1; $p += 1) {
         /**
          * date
          */
        $date = new Zend_Date();
        $date->setTimestamp($purchaseDetails[$p]['date']);
        $temp['date'] = $date->get(Zend_Date::DATE_MEDIUM);
            
        /**
         * party name
         */
        $vendorModel = new Core_Model_Finance_Vendor(
                                    $purchaseDetails[$p]['vendor_id']);
        $temp['particulars'] = $vendorModel->getName();
            
        /**
         * Type
         */
         $temp['type'] = "Purchase";
            
         /**
          * Invoice Id
          */
          $temp['id'] = $purchaseDetails[$p]['purchase_id'];
            
         /**
          * Invoice Tax
          */
          $this->setPurchaseId($purchaseDetails[$p]['purchase_id']);
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
              $purchaseRegister[] = $temp;
       }// end of invoice loop
        return $purchaseRegister;
    }
    
    /**
     * @return array 
     */
    public function fetchPurchaseByDate($date)
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
    
    /**
     * Create a finance Purchase
     * @param array $itemsData with keys and $data with keys 
     * @return int ledger ID 
     */
    public function vocherEntry($itemsData = array(), $data = array())
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $purchaseData = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'vendor_id' => $data['vendor_id'],
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'payment_terms' => $data['payment_terms'],
            'type' => $data['voucher_entry']
        );
        
        $this->_purchaseId = parent::create($purchaseData);
        
        $purchaseItemModel = new Core_Model_Finance_Purchase_ItemOthers;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $purchaseItemId = $purchaseItemModel->create($itemsData[$i], 
                                                            $this->_purchaseId);
        }
        
        $vendorModel = new Core_Model_Finance_Vendor($data['vendor_id']);
        $vendorLedgerId = $vendorModel->getLedgerId();
        $vendorName = $vendorModel->getName();
        $notes = 'Purchase from '.$vendorName;
        $vendorLedgerEntryId = $this->vendorLedgerEntry($vendorLedgerId, $notes);
        $taxLedgerEntries = $this->taxLedgerEntry($notes);
        
        $fa_ledger_entry_ids = array();
        $fa_ledger_entry_ids['0'] = $vendorLedgerEntryId;
        if ($data['voucher_entry'] == 1) {
            $fa_ledger_entry_ids['1'] = $this->assestLedgerEntry();
        }
        else {
            $fa_ledger_entry_ids['1'] = $this->expenseLedgerEntry();
        }   
        
        for($i = 0; $i <= sizeof($taxLedgerEntries)-1; $i += 1) {
                array_push($fa_ledger_entry_ids, $taxLedgerEntries[$i]);
        }
        $fa_ledger_entry_ids = serialize($fa_ledger_entry_ids);
        $table = $this->getTable();
        $dataToUpdate['s_ledger_ids'] = $fa_ledger_entry_ids;
        $where = $table->getAdapter()->quoteInto('purchase_id = ?',
                                                            $this->_purchaseId);
        $result = $table->update($dataToUpdate, $where);
        
        return $this->_purchaseId;
    }
    
    /**
     * Creates a row in the expense ledger
     * @param float amount
     * @return ledger entry id
     */
    public function expenseLedgerEntry()
    {
        $items = $this->getVoucherItems();
               
        $financeLedger = new Core_Model_Finance_Ledger;
        $expenseLedgerRecord = $financeLedger->fetchByName('Indirect expense');
        
        $totalPrice = 0;
        for($i = 0; $i <= sizeof($items)-1; $i += 1) {
             $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
             $totalPrice = $totalPrice + $price;
        }
        $notes = 'Purchase with Purchase Id = '.$this->_purchaseId;   
        
        $dataToInsert = array(
             'debit' => $amount,
             'credit' => "0",
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $expenseLedgerRecord['fa_ledger_id']
           );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * Creates a row in the assest ledger
     * @param float amount
     * @return ledger entry id
     */
    public function assestLedgerEntry()
    {
        $items = $this->getVoucherItems();
        
        $financeLedger = new Core_Model_Finance_Ledger;
        $assestLedgerRecord = $financeLedger->fetchByName('Current Asset');
        
        $totalPrice = 0;
        for($i = 0; $i <= sizeof($items)-1; $i += 1) {
             $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
             $totalPrice = $totalPrice + $price;
        }
        $notes = 'Purchase with Purchase Id = '.$this->_purchaseId;  
        
        $dataToInsert = array(
             'debit' => $amount,
             'credit' => "0",
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $assestLedgerRecord['fa_ledger_id']
           );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * @param array $itemsData with keys and $data with keys 
     * updates bankaccount details and ledger entries
     * @return int
     */
    public function voucherEdit($itemsData = array(), $data = array()) 
    {
        $table = $this->getTable();
        
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $dataToUpdate = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'vendor_id' => $data['vendor_id'],
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'payment_terms' => $data['payment_terms'],
            'type' => $data['voucher_entry']
        ); 
        
        $where = $table->getAdapter()->quoteInto('purchase_id = ?',
                                                            $this->_purchaseId);
        $table->update($dataToUpdate, $where);
                
        $purchaseItemModel = new Core_Model_Finance_Purchase_ItemOthers;
        $this->getVoucherItemModel()->setPurchaseModel($this)->deleteAll();
        
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $purchaseItemId = $purchaseItemModel->create($itemsData[$i], 
                                                            $this->_purchaseId);
        }
        
        $LedgerEntyModel = new Core_Model_Finance_Ledger_Entry;
        $purchaseRecord = $this->fetch();
        $ledgerEntryIds = unserialize($purchaseRecord['s_ledger_ids']);
        
        for($i = 0; $i <= sizeof($ledgerEntryIds)-1; $i += 1) {
            $result = $LedgerEntyModel->deleteById($ledgerEntryIds[$i]);
        }
        
        $vendorModel = new Core_Model_Finance_Vendor($data['vendor_id']);
        $vendorLedgerId = $vendorModel->getLedgerId();
        $vendorName = $vendorModel->getName();
        $notes = 'Purchase from '.$vendorName;
        $vendorLedgerEntryId = $this->vendorLedgerEntry($vendorLedgerId, $notes);
        $taxLedgerEntries = $this->taxLedgerEntry($notes);
        
        $fa_ledger_entry_ids = array();
        $fa_ledger_entry_ids['0'] = $vendorLedgerEntryId;
        if ($data['voucher_entry'] == 1) {
            $fa_ledger_entry_ids['1'] = $this->assestLedgerEntry();
        }
        else {
            $fa_ledger_entry_ids['1'] = $this->expenseLedgerEntry();
        }  
        for($i = 0; $i <= sizeof($taxLedgerEntries)-1; $i += 1) {
                array_push($fa_ledger_entry_ids, $taxLedgerEntries[$i]);
        }
        $fa_ledger_entry_ids = serialize($fa_ledger_entry_ids);
        $table = $this->getTable();
        $dataToUpdate['s_ledger_ids'] = $fa_ledger_entry_ids;
        $where = $table->getAdapter()->quoteInto('purchase_id = ?',
                                                            $this->_purchaseId);
        $result = $table->update($dataToUpdate, $where);
        
        $log = $this->getLoggerService();
        $info = 'Purchase edited with purchas id = '. $this->_purchaseId;
        $log->info($info);
        
        return $result;
    }
}



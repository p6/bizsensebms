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
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_PurchaseReturn extends Core_Model_Abstract
{
    /**
     * @var the Purchase Return ID
     */
	 protected $_purchaseReturnId;
    
     /**
     * @var to store date for transaction
     */
    protected $_transactionTime;
    
    /**
     * @param purchaseReturnId
     */
     public function __construct($purchaseReturnId = null)
     {
        if (is_numeric($purchaseReturnId)) {  
            $this->_purchaseReturnId = $purchaseReturnId;
        }
        parent::__construct();
     }

	/**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_PurchaseReturn';
    
    /**
     * @var object the Purchase Return item model
     */
    protected $_itemModel;
    
    /**
     * @var object the ledger entry model
     */
    protected $_ledgerEntryModel;
    
	/**
     * @param int $purchaseReturnId
     * @return fluent interface
     */
    public function setPurchaseReturnId($purchaseReturnId)
    {
        $this->_purchaseReturnId = $purchaseReturnId;
        return $this;
    }

    /**
     * @return int the purchase Return ID
     */
    public function getPurchaseReturnId()
    {
        return $this->_purchaseReturnId;
    }
    
    /**
     * Create a purchase Return
     * @param array $data with keys 
     * @return int purchase Return ID 
     */
    public function create($itemsData = array(),$data = array(),$purchaseId)
    {
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $purchaseReturnData = array(
            'purchase_id' => $purchaseId,
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'customer_purchase_return_reference' => 
                                     $data['customer_purchase_return_reference']
        );
        
        $this->_purchaseReturnId = parent::create($purchaseReturnData);
        
        $purchaseReturnItemModel = new Core_Model_PurchaseReturn_Item;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $purchaseReturnItemId = $purchaseReturnItemModel->create(
                                    $itemsData[$i], $this->_purchaseReturnId);
        }
        
        $purchaseModel = new Core_Model_Finance_Purchase($purchaseId);
        $purchaseRecord = $purchaseModel->fetch();
        $vendorModel = new Core_Model_Finance_Vendor(
                                                $purchaseRecord['vendor_id']);
        $vendorLedgerId = $vendorModel->getLedgerId();
        $vendorName = $vendorModel->getName();
        $notes = 'Purchase Return from '.$vendorName;
        $vendorLedgerEntryId = $this->vendorLedgerEntry(
                                                     $vendorLedgerId, $notes);
        $taxLedgerIds = $this->taxLedgerEntry($notes);
        if ($taxLedgerIds != '') {
            $taxLedgerEntries = $taxLedgerIds;
        }
        $fa_ledger_entry_ids = array();
        $fa_ledger_entry_ids['0'] = $vendorLedgerEntryId;
        $fa_ledger_entry_ids['1'] = $this->purchaseAccountLedgerEntry();
        
        $purchaseModel = new Core_Model_Finance_Purchase($purchaseId);
        $purchaseRecord = $purchaseModel->fetch();
        
        if ($purchaseRecord['discount_amount'] != 0 || $purchaseRecord['discount_amount'] != null) {
            $ledgerEntryId = $this->discountLedgerEntry(
                                   $purchaseRecord['discount_amount'], $notes);
            array_push($fa_ledger_entry_ids, $ledgerEntryId);
        }
        
        if ($purchaseRecord['freight_amount'] != 0 || 
                                $purchaseRecord['freight_amount'] != null) {
            $ledgerEntryId = $this->freightLedgerEntry(
                                        $purchaseRecord['freight_amount'],$notes );
            array_push($fa_ledger_entry_ids, $ledgerEntryId);
        }
        
        for($i = 0; $i <= sizeof($taxLedgerEntries)-1; $i += 1) {
                array_push($fa_ledger_entry_ids, $taxLedgerEntries[$i]);
        }
        $fa_ledger_entry_ids = serialize($fa_ledger_entry_ids);
        $table = $this->getTable();
        $dataToUpdate['s_ledger_entry_ids'] = $fa_ledger_entry_ids;
        $where = $table->getAdapter()->quoteInto('purchase_return_id = ?', 
                                                $this->_purchaseReturnId);
        $table->update($dataToUpdate, $where);
        
        return $this->_purchaseReturnId;
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
             'debit' => $amount,
             'credit' => "0",
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $discountLedgerRecord['fa_ledger_id']
           );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
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
             'debit' => "0",
             'credit' => $amount,
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
             'debit' => "0",
             'credit' =>  $totalPrice,
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $salesLedgerRecord['fa_ledger_id']
           );
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgerEntryId = $ledgerEntryModel->create($dataToInsert);
        return $ledgerEntryId;
    }
    /**
     * Creates a row in the Vendor ledger
     * @return ledger Id
     */
    public function vendorLedgerEntry($ledgerId, $notes)
    {
        $dataToInsert['debit'] = $this->getTotalAmount();
        $dataToInsert['credit']= "0";
        $dataToInsert['notes'] = $notes;
        $dataToInsert['transaction_timestamp'] = $this->_transactionTime;
        $dataToInsert['fa_ledger_id'] = $ledgerId;
        $ledgerEntryId = $this->getLedgerEntryModel()->create($dataToInsert);
        return $ledgerEntryId;
    }
    
    /**
     * Creates a row in the tax ledger
     * @return array of tax ledger entry ids
     */
    public function taxLedgerEntry($notes)
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
            $ledgerEntryId = $this->getLedgerEntryModel()->create($dataToInsert);
            array_push($ledgerEntryIds, $ledgerEntryId);
           }
           return $ledgerEntryIds;
    }
    
    /**
     * @return array the purchaseReturn items
     */
    public function getItems()
    {
        $table = $this->getItemModel()->getTable();
        $select = $table->select();
        $select->where('purchase_return_id = ?', $this->_purchaseReturnId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return object Core_Model_purchaseReturn_Item the dependant 
     * purchase return item model
     * @return object of Core_Model_PurchaseReturn_Item
     */
    public function getItemModel()
    {
        if (null === $this->_itemModel) {
            $this->_itemModel = new Core_Model_PurchaseReturn_Item;
        }
        return $this->_itemModel;
    }
    
    /**
     * @return json Purchase Items
     */
    public function getItemsJson()
    {
        $items = $this->getItems();
        $itemToReturn = array();
        foreach ($items as $item) {
            $temp = $item;
            $temp['product_id'] = $temp['product_id'];
            $itemToReturn[] = $temp;
        }
        return $itemToReturn;
    }
    
    /**
     * Feteches a record from the purchase Return table
     * @return result object from Zend_Db_Select object
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()->where(
            'purchase_return_id = ?', $this->_purchaseReturnId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return float the purchase return total amount
     */
    public function getTotalAmount()
    {
        $purchaseRecord = $this->fetch();
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
        return $total;
    }
    
    /**
     * @return object Core_Model_Finance_Ledger_Entry the dependant 
     * purchase return item model
     */
    public function getLedgerEntryModel()
    {
        if (null === $this->_ledgerEntryModel) {
            $this->_ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        }
        return $this->_ledgerEntryModel;
    }
    
    /**
    * Edit the Purchase Return
    * @param array $data the data of the Purchase Return
    * @param array $itemsData the invoice items that have changed
    * @return bool
    */ 
    public function edit($itemsData = array(), $data = array()) 
    {  
        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();
        
        $returnLedgerRecord = $this->fetch();
        $ledgerEntryIds = unserialize(
                                 $returnLedgerRecord['s_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $purchaseModel = new Core_Model_Finance_Purchase(
                                           $returnLedgerRecord['purchase_id']);
        $purchaseRecord = $purchaseModel->fetch();
        $vendorModel = new Core_Model_Finance_Vendor(
                                                $purchaseRecord['vendor_id']);
        $vendorLedgerId = $vendorModel->getLedgerId();
        $vendorName = $vendorModel->getName();
        $notes = 'Purchase Return from '.$vendorName;
        $vendorLedgerEntryId = $this->vendorLedgerEntry(
                                                     $vendorLedgerId, $notes);
        $taxLedgerIds = $this->taxLedgerEntry($notes);
        
        if ($taxLedgerIds != '') {
            $taxLedgerEntries = $taxLedgerIds;
        }
        $fa_ledger_entry_ids = array();
        $fa_ledger_entry_ids['0'] = $vendorLedgerEntryId;
        $fa_ledger_entry_ids['1'] = $this->purchaseAccountLedgerEntry();
        for($i = 0; $i <= sizeof($taxLedgerEntries)-1; $i += 1) {
                array_push($fa_ledger_entry_ids, $taxLedgerEntries[$i]);
        }
        $fa_ledger_entry_ids = serialize($fa_ledger_entry_ids);
        
               
        $table = $this->getTable();
               
        $dataToUpdate = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            's_ledger_entry_ids' =>  $fa_ledger_entry_ids,
            'customer_purchase_return_reference' => 
                                     $data['customer_purchase_return_reference']
        );

        $where = $table->getAdapter()->quoteInto('purchase_return_id = ?', 
                                                      $this->_purchaseReturnId);
        $result = $table->update($dataToUpdate, $where);

        $this->getItemModel()->setPurchaseReturnModel($this)->deleteAll();
       
        $purchaseReturnItemModel = new Core_Model_PurchaseReturn_Item;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $purchaseReturnItemId = $purchaseReturnItemModel->create(
                                $itemsData[$i], $this->_purchaseReturnId);
        }
        
        return $result;
        
    }    
    
    /** 
     * Deletes a row in the Purchase Return table
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
            'purchase_return_id = ?', $this->_purchaseReturnId
        );
        $result = $table->delete($where);
        
        return $result;
    }
    
    /**
     * @return Purchase Return Register
     */
    public function purchaseReturnRegister()
    {
        $purchaseReturnDetails = $this->fetchAll();
        
        $no_value = array();
        if (!$purchaseReturnDetails) {
            return $no_value;
        }
        for($pr = 0; $pr <= sizeof($purchaseReturnDetails)-1; $pr += 1) {
         /**
          * date
          */
        $date = new Zend_Date();
        $date->setTimestamp($purchaseReturnDetails[$pr]['date']);
        $temp['date'] = $date->get(Zend_Date::DATE_MEDIUM);
            
        /**
         * party name
         */
        $purchaseModel = new Core_Model_Finance_Purchase(
                                    $purchaseReturnDetails[$pr]['purchase_id']);
        $purchaseReturnRecord = $purchaseModel->fetch();
        $vendorModel = new Core_Model_Finance_Vendor(
                                    $purchaseReturnRecord['vendor_id']);
        $temp['particulars'] = $vendorModel->getName();
                    
        /**
         * Type
         */
         $temp['type'] = "Purchase Return";
            
         /**
          * Invoice Id
          */
          $temp['id'] = $purchaseReturnDetails[$pr]['purchase_return_id'];
           
         /**
          * Invoice Tax
          */
          $this->setPurchaseReturnId(
                         $purchaseReturnDetails[$pr]['purchase_return_id']);
          
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
              $purchaseReturnRegister[] = $temp;
             
       }// end of invoice loop
      return $purchaseReturnRegister;
    }
    
    /**
     * @return array 
     */
    public function fetchPurchaseReturnByDate($date)
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

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
 * @category BizSense
 * @package  Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Invoice extends Core_Model_Abstract
{
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';


    /**
     * Invoice to whom - contact or account
     * Contact is consumer - B2C
     * Account is organization - B2B
     */
    const TO_TYPE = 'inoice to type';
    const TO_TYPE_ACCOUNT = 1;
    const TO_TYPE_CONTACT = 2;


    /**
     * The type of invoice - service invoice | product invoice | all
     * We also have INVOICE_ITEM_TYPE_SERVICE which is a better way 
     * to indicate whether the item is of type product or service
     */
    const INVOICE_TYPE = 'invoice type';
    const INVOICE_TYPE_SERVICE = 1;
    const INVOICE_TYPE_PRODUCT = 2;
    const INVOICE_TYPE_HYBRID = 3;


    /**
     * The invoice item type - product or service
     */
    const INVOICE_ITEM = 'invoice item';
    const INVOICE_ITEM_TYPE_SERVICE = 1;
    const INVOICE_ITEM_TYPE_PRODUCT = 2;

    /**
     * @var the invoice id
     */
    protected $_invoiceId;

     /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Invoice';

    /**
     * @var array the invoice meta data with the array keys
     * invoice_type, created_by, branch_id, to_type, to_type_id    
     */
    protected $_metaData = array();

    /**
     * @var array the items of the invoive 
     */
    protected $_itemsData = array();


    /**
     * @var object the invoice item model
     */
    protected $_itemModel;
    
    /**
     * @var object the invoice item model
     */
    protected $_serviceItemModel;
    
    /**
     * @var object the ledger entry model
     */
    protected $_ledgerEntryModel;

    /**
     * @var object the invoice settings
     */
    protected $_invoiceSettings;
    
    /**
     * @var to store date for transaction
     */
    protected $_transactionTime;

    /**
     * @var to store invoice to 
     */
    protected $_invoiceTo;

    /**
     * @param int invoiceId
     */
    public function __construct($invoiceId = null)
    {
        $this->_invoiceId = $invoiceId;
    }

    /**
     * @return int the invoice id
     */
    public function getId()
    {
        return $this->_invoiceId;
    }

    /**
     * @return int the invoice id
     */
    public function getInvoiceId()
    {
        return $this->_invoiceId;
    }

    /**
     * @param int $invoiceId the invoice ID
     * @return object Core_Model_Invoice
     */
    public function setInvoiceId($invoiceId)
    {
        $this->_invoiceId = $invoiceId;
        return $this;
    }
    
    /**
     * @return object Core_Model_Finance_Ledger_Entry the dependant 
     * invoice item model
     */
    public function getLedgerEntryModel()
    {
        if (null === $this->_ledgerEntryModel) {
            $this->_ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        }
        return $this->_ledgerEntryModel;
    }

    /**
     * Feteches a record from the lead table
     * @return result object from Zend_Db_Select object
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()->where(
            'invoice_id = ?', $this->_invoiceId
        );
        $result = $table->fetchRow($select);
        return $result->toArray();
    }

    
    /**
     * @param array $itemsData of arrays of service item rows
     * @param array $data of invoice meta data like branch_id
     * @return int invoice id
     */
    public function create($itemsData = array(), $data = array())
    {
        if ($data['to_type'] == 1) {
            $toType = Core_Model_Invoice::TO_TYPE_ACCOUNT;
            $toTypeId = $data['account_id'];
            $toTypeName = "account";
        } else {
            $toType = Core_Model_Invoice::TO_TYPE_CONTACT;
            $toTypeId = $data['contact_id'];
            $toTypeName = "contact";
        }

        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }

        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();

        if (is_numeric($data['contact_id'])) {
            $contactId = $data['contact_id'];
        } else {
            $contactId = null;
        }
        
        $invoiceData = array(
            'invoice_type' => Core_Model_Invoice::INVOICE_TYPE_PRODUCT,
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'to_type' => $toType,
            'to_type_id' => $toTypeId,
            'contact_id' => $contactId,
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'payment_terms' => $data['payment_terms'],
            'delivery_terms' => $data['delivery_terms'],
            'purchase_order' => $data['purchase_order'],
            'campaign_id' => $data['campaign_id'],
            'freight_amount' => $data['freight_amount'],
            'discount_amount' => $data['discount_amount']
        );
        
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $itemsData[$i]['invoice_item_type'] = 
                                  Core_Model_Invoice::INVOICE_ITEM_TYPE_SERVICE;
            $itemsData[$i]['invoice_item_inventory_id'] = 
                                                   $itemsData[$i]['product_id'];
            unset($itemsData[$i]['product_id']);
        }

        $this->_invoiceId = $this->_create($invoiceData, $itemsData);
        
        if ($toTypeName == "account")  {
            $accountModel =  new Core_Model_Account($data['account_id']);
            $this->_invoiceTo = 'Type = Account  Name = '.
                                                $accountModel->getName();
            $this->ledgerEntries($accountModel->getLedgerId());
        }
        
        if ($toTypeName == "contact") {
            $contactModel =  new Core_Model_Contact($data['contact_id']);
            $this->_invoiceTo = 'Type = Contact  Name = '.
                                                $contactModel->getFullName();
            $this->ledgerEntries($contactModel->getLedgerId());
        }
        
        return $this->_invoiceId;
    }
    
    /**
     * invoice ledger entries and update ledger entry ids to invoice 
     * table(s_ledger_entry_ids)
     * @return int
     */
    public function ledgerEntries($ledgerId)
    {
        $fa_ledger_entry_ids = array(
                        '0' => $this->customerLedgerEntry($ledgerId),
                        '1' => $this->salesAccountLedgerEntry()
                        );
        $invoiceRecord = $this->fetch();
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
        $where = $table->getAdapter()->quoteInto('invoice_id = ?', 
                                                           $this->_invoiceId);
        $result = $table->update($dataToUpdate, $where);
        
        return $result;
    }
    
    /**
     * Creates a row in the customer ledger
     * @return ledger entry id
     */
    public function customerLedgerEntry($ledgerId)
    {
        $notes = 'Invoice with Invoice Id = '.$this->_invoiceId.
                 '  '.$this->_invoiceTo;
        $dataToInsert = array(
             'debit' => $this->getTotalAmount(),
             'credit' =>  "0",
             'notes' => $notes,
             'transaction_timestamp' => $this->_transactionTime,
             'fa_ledger_id' => $ledgerId
           );
       $ledgerEntryId = $this->getLedgerEntryModel()->create($dataToInsert);
       return $ledgerEntryId;
    }

    /**
     * Creates a row in the sales account ledger
     * calculate total sales price by items
     * @return ledger entry id
     */
    public function salesAccountLedgerEntry()
    {
        $items = $this->getItems();
        $invoiceRecord = $this->fetch();
        
        $financeLedger = new Core_Model_Finance_Ledger;
        $salesLedgerRecord = $financeLedger->fetchByName('Sales Account');
        
        $totalPrice = 0;
        for($i = 0; $i <= sizeof($items)-1; $i += 1) {
             if ($invoiceRecord['invoice_type'] == 1) {
                $items[$i]['quantity'] = 1;
                $items[$i]['unit_price'] = $items[$i]['amount'];
             }
             $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
             $totalPrice = $totalPrice + $price;
        }
        $notes = 'Invoice with Invoice Id = '.$this->_invoiceId.
                 '  '.$this->_invoiceTo;
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
     * Creates a row in the tax ledger
     * Calculate total amount of tax for each tax type and credit amount to 
     * repective tax ledger 
     */
    public function taxLedgerEntry()
    {
        $items = $this->getItems();
        $invoiceRecord = $this->fetch();
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
                   if ($invoiceRecord['invoice_type'] == 1) {
                        $items[$i]['quantity'] = 1;
                        $items[$i]['unit_price'] = $items[$i]['amount'];
                   }
                   $price = $items[$i]['unit_price'] * $items[$i]['quantity'];
                   $taxPercentage = $taxTypeModel->getTaxPercentageFromId(
                                                     $items[$i]['tax_type_id']);
                   $taxAmount = ($price * $taxPercentage) /  100 ;
                   $totalTaxAmount = $totalTaxAmount + $taxAmount;                   
                }
            }
            $financeLedger = new Core_Model_Finance_Ledger;
            $salesLedgerRecord = $financeLedger->fetchByName($tax_types[$tax]);
            $notes = 'Invoice with Invoice Id = '.$this->_invoiceId.
                 '  '.$this->_invoiceTo;
            $dataToInsert = array(
                'debit' => "0",
                'credit' =>  $totalTaxAmount,
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
     * Creates a row in the lead table
     * @param array $data to be stored
     */
    protected function _create($invoiceData = array(), $itemsData)
    {
        $this->_metaData = $invoiceData;
        $this->_itemsData = $itemsData;
        $this->createMeta();
        $this->createItems();
        return $this->_invoiceId;
    }

    /**
     * Create the meta data
     * @return int the invoice id
     */
    public function createMeta()
    {
        $table = $this->getTable();
        $invoiceData = $this->_metaData;
        $invoiceData = $this->unsetNonTableFields($invoiceData);
        $this->_invoiceId = $table->insert($invoiceData);
    }

    /**
     * Create the invoice items
     * @return void
     */
    public function createItems()
    {
        $invoiceItems = $this->_itemsData;   
        for ($i = 0; $i < count($invoiceItems); $i++) {
            $invoiceItems[$i]['invoice_id'] = $this->_invoiceId;
            $this->getItemModel()->create($invoiceItems[$i]);  
        }
    }


   /**
    * Edit the invoice
    * @param array $data the meta data of the invoice
    * @param array $itemsData the invoice items that have changed
    */ 
    public function edit($itemsData = array(), $data = array()) 
    {  
        $this->prepareEphemeral();    
        $this->_metaData = $data;
        $this->_itemsData = $itemsData;
        $table = $this->getTable();

        $dataToUpdate = array();
        if ($data['to_type'] == Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            $dataToUpdate['to_type_id'] = $data['account_id'];
            $toTypeName = "account";
        } else {
            $dataToUpdate['to_type_id'] = $data['contact_id'];
            $toTypeName = "contact";
        }
        $dataToUpdate['branch_id'] = $data['branch_id'];
        $dataToUpdate['to_type'] = $data['to_type'];
        $dataToUpdate['notes'] = $data['notes'];
        $dataToUpdate['purchase_order'] = $data['purchase_order'];
        $dataToUpdate['delivery_terms'] = $data['delivery_terms'];
        $dataToUpdate['payment_terms'] = $data['payment_terms'];
        $dataToUpdate['freight_amount'] = $data['freight_amount'];
        $dataToUpdate['discount_amount'] = $data['discount_amount'];

        if (!is_numeric($data['campaign_id'])) {
            $dataToUpdate['campaign_id'] = null;
        } else {
            $dataToUpdate['campaign_id'] = $data['campaign_id'];
        }
        

        if (is_numeric($data['contact_id'])) {
            $contactId = $data['contact_id'];
        } else {
            $contactId = null;
        }

        
        $dataToUpdate['contact_id'] = $contactId;

        $date = new Zend_Date($this->_metaData['date']);
        $this->_transactionTime = $date->getTimestamp();
        $dataToUpdate['date'] = $date->getTimestamp();

        $where = $table->getAdapter()->quoteInto('invoice_id = ?',
                                                             $this->_invoiceId);
        $table->update($dataToUpdate, $where);

        $this->getItemModel()->setInvoiceModel($this)->deleteAll();
        for ($i = 0; $i < count($itemsData); $i++) {
            $itemsData[$i]['invoice_id'] = $this->_invoiceId;
            $itemsData[$i]['invoice_item_type'] = 1;
            $itemsData[$i]['invoice_item_inventory_id'] = 
                                                   $itemsData[$i]['product_id'];
            $this->getItemModel()->create($itemsData[$i]);  
        } 
        
        
        $invoiceData = $this->fetch();
        $ledgerEntryIds = unserialize($invoiceData['s_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
       
        
        if ($toTypeName == "account")  {
            $accountModel =  new Core_Model_Account($data['account_id']);
            $this->_invoiceTo = 'Type = Account  Name = '.
                                                $accountModel->getName();
            $this->ledgerEntries($accountModel->getLedgerId());
        }
        
        if ($toTypeName == "contact") {
            $contactModel =  new Core_Model_Contact($data['contact_id']);
            $this->_invoiceTo = 'Type = Contact  Name = '.
                                                $contactModel->getFullName();
            $this->ledgerEntries($contactModel->getLedgerId());
        }
    }    

    /** 
     * Deletes a row in the invoice table
     * Deletes related ledger entries in ledger_entry table
     */
    public function delete()
    {
        $this->prepareEphemeral();    
        
        $invoiceData = $this->fetch();
        $ledgerEntryIds = unserialize($invoiceData['s_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
        
        $salesReturnModel = new Core_Model_SalesReturn;
        $salesReturnModel->deleteByInvoiceId($this->_invoiceId);
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto(
            'invoice_id = ?', $this->_invoiceId
        );
        $result = $table->delete($where);
       
        
        return $result;
    }

    /**
     * @return object Core_Model_Invoice_Item the dependant invoice item model
     */
    public function getItemModel()
    {
        if (null === $this->_itemModel) {
            $this->_itemModel = new Core_Model_Invoice_Item;
        }
        return $this->_itemModel;
    }


     /**
     * @return object Core_Model_Invoice_Item the dependant invoice item model
     */
    public function getServiceItemModel()
    {
        if (null === $this->_serviceItemModel) {
            $this->_serviceItemModel = new Core_Model_Invoice_ServiceItem;
        }
        return $this->_serviceItemModel;
    }
    
    public function getDebitEntryId()
    {
        $data = $this->fetch();
        return $data['debit_entry_id'];
    }

    /**
     * The invoice is deleted
     * Delete the corresponding entries in the financial account books
     */
    public function updateFinanceDeleteInvoice()
    {
        $ephemeralData = $this->getEphemeralData();
        if ($ephemeralData['to_type'] == self::TO_TYPE_ACCOUNT) {
            $financeModel = new Core_Model_Finance_Account_Account;        
        } else {
            $financeModel = new Core_Model_Finance_Account_Contact;
        }
        $financeModel->setId($ephemeralData['debit_entry_id']);
        $financeModel->deleteDebit();
    }

    /**
     * @return array the invoice items
     */
    public function getItems()
    {
        $invoiceRecord = $this->fetch();
        if ($invoiceRecord['invoice_type'] == 1) {
            $table = $this->getServiceItemModel()->getTable();
        }
        else {
            $table = $this->getItemModel()->getTable();
        }
        $select = $table->select();
        $select->where('invoice_id = ?', $this->_invoiceId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return array the invoice service items
     */
    public function getServiceItems()
    {
        $table = $this->getServiceItemModel()->getTable();
        $select = $table->select();
        $select->where('invoice_id = ?', $this->_invoiceId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return float the invouce total amount
     */
    public function getTotalAmount()
    {
        $invoiceRecord = $this->fetch();
        $invoiceItems = $this->getItems();
        $total = 0;
        foreach ($invoiceItems as $item) { 
            if ($invoiceRecord['invoice_type'] == 1) {
                $item['quantity'] = 1;
                $item['unit_price'] = $item['amount'];
            }
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
     * @return array the invoice items
     */
    public function getInvoiceItems()
    {
        $invoiceData = $this->fetch();
        if ($invoiceData['to_type'] == Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            $invoiceData['account_id'] = $invoiceData['to_type_id'];
        } else {
            $invoiceData['contact_id'] = $invoiceData['to_type_id'];
        }
        return $invoiceData; 
    }
    
    /**
     * Product Items Json
     */
    public function getItemsJson()
    {
        $items = $this->getItems();
        $itemToReturn = array();
        
        foreach ($items as $item) {
            $temp = $item;
            if  (array_key_exists('service_item_id', $temp))  {
                $temp['product_id'] = $temp['service_item_id'];
            } 
            else {                          
                $temp['product_id'] = $temp['invoice_item_inventory_id'];
            }
            $itemToReturn[] = $temp;
        }
        return $itemToReturn;
    }
    
    /**
     * @return string PDF file location 
     */
    public function getPdfFileLocation()
    {       
        $pdf = new Core_Model_Invoice_Pdf();
        $pdf->setModel($this);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/documents/invoice/invoice_' . 
                                                     $this->_invoiceId . '.pdf';
        $pdf->Output($pdfPath, 'F');
        return $pdfPath; 
    }

    /**
     * @return string service invoice PDF file location 
     */
    public function getServicePdfFileLocation()
    {       
        $pdf = new Core_Model_Invoice_ServicePdf();
        $pdf->setModel($this);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/documents/invoice/invoice_' . 
                                                     $this->_invoiceId . '.pdf';
        $pdf->Output($pdfPath, 'F');
        return $pdfPath; 
    }

    /**
     * @return object Core_Model_Invoice_Settings
     */
    public function getSettings()
    {
        if (!$this->_invoiceSettings) {
            $this->_invoiceSettings =  new Core_Model_Invoice_Settings();
        }
        return $this->_invoiceSettings;
    }
        
    /**
     * @return Sales Register
     */
    public function salesRegister()
    {
        $invoiceDetails = $this->fetchAll();
        
        $no_value = array();
        if (!$invoiceDetails) {
            return $no_value;
        }
        
        for($invoice = 0; $invoice <= sizeof($invoiceDetails)-1; $invoice += 1) {
         /**
          * date
          */
        $date = new Zend_Date();
        $date->setTimestamp($invoiceDetails[$invoice]['date']);
        $temp['date'] = $date->get(Zend_Date::DATE_MEDIUM);
            
        /**
         * party name
         */
        if ($invoiceDetails[$invoice]['to_type'] == 
                                    Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            $accountModel = new Core_Model_Account(
                               $invoiceDetails[$invoice]['to_type_id']);
            $partyName = $accountModel->getName();
        } 
        else {
            $contactModel = new Core_Model_Contact(
            $invoiceDetails[$invoice]['to_type_id']);
            $partyName = $contactModel->getFullName();
        }
        $temp['particulars'] = $partyName;
            
        /**
         * Type
         */
         $temp['type'] = "Invoice";
            
         /**
          * Invoice Id
          */
          $temp['id'] = $invoiceDetails[$invoice]['invoice_id'];
            
         /**
          * Invoice Tax
          */
          $this->setInvoiceId($invoiceDetails[$invoice]['invoice_id']);
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
                    if ($invoiceDetails == 1) {
                        $items[$i]['quantity'] = 1;
                        $items[$i]['unit_price'] = $item['amount'];
                    }
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
    public function fetchInvoiceByDate($date)
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
     * @param int campaignId
     * @return array the invoices record with campaignId
     */
    public function getInvoicesByCampaignId($campaignId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('campaign_id = ?', $campaignId);

        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param array $itemsData of arrays of service item rows
     * @param array $data of invoice meta data like branch_id
     * @return int invoice id
     */
    public function createServiceInvoice($itemsData = array(), $data = array())
    {
        if ($data['to_type'] == 1) {
            $toType = Core_Model_Invoice::TO_TYPE_ACCOUNT;
            $toTypeId = $data['account_id'];
            $toTypeName = "account";
        } else {
            $toType = Core_Model_Invoice::TO_TYPE_CONTACT;
            $toTypeId = $data['contact_id'];
            $toTypeName = "contact";
        }

        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }

        $date = new Zend_Date($data['date']);
        $this->_transactionTime = $date->getTimestamp();

        if (is_numeric($data['contact_id'])) {
            $contactId = $data['contact_id'];
        } else {
            $contactId = null;
        }

        $invoiceData = array(
            'invoice_type' => Core_Model_Invoice::INVOICE_TYPE_SERVICE,
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'to_type' => $toType,
            'to_type_id' => $toTypeId,
            'contact_id' => $contactId,
            'date' => $date->getTimestamp(),
            'notes' => $data['notes'],
            'payment_terms' => $data['payment_terms'],
            'delivery_terms' => $data['delivery_terms'],
            'purchase_order' => $data['purchase_order'],
            'campaign_id' => $data['campaign_id']
        );
        
        $this->_invoiceId = parent::create($invoiceData);
        
        $serviceItemModel = new Core_Model_Invoice_ServiceItem;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $serviceItemId = $serviceItemModel->create($itemsData[$i], 
                                                            $this->_invoiceId);
        }

        if ($toTypeName == "account")  {
            $accountModel =  new Core_Model_Account($data['account_id']);
            $this->_invoiceTo = 'Type = Account  Name = '.
                                                $accountModel->getName();
            $this->ledgerEntries($accountModel->getLedgerId());
        }
        
        if ($toTypeName == "contact") {
            $contactModel =  new Core_Model_Contact($data['contact_id']);
            $this->_invoiceTo = 'Type = Contact  Name = '.
                                                $contactModel->getFullName();
            $this->ledgerEntries($contactModel->getLedgerId());
        }
        
        return $this->_invoiceId;
    }
    
        
    /**
    * Edit the invoice
    * @param array $data the meta data of the invoice
    * @param array $itemsData the invoice items that have changed
    */ 
    public function editServiceInvoice($itemsData = array(), $data = array()) 
    {  
        $this->_metaData = $data;
        $this->_itemsData = $itemsData;
        $table = $this->getTable();

        $dataToUpdate = array();
        if ($data['to_type'] == Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            $dataToUpdate['to_type_id'] = $data['account_id'];
            $toTypeName = "account";
        } else {
            $dataToUpdate['to_type_id'] = $data['contact_id'];
            $toTypeName = "contact";
        }
        $dataToUpdate['branch_id'] = $data['branch_id'];
        $dataToUpdate['to_type'] = $data['to_type'];
        $dataToUpdate['notes'] = $data['notes'];
        $dataToUpdate['purchase_order'] = $data['purchase_order'];
        $dataToUpdate['delivery_terms'] = $data['delivery_terms'];
        $dataToUpdate['payment_terms'] = $data['payment_terms'];

        if (!is_numeric($data['campaign_id'])) {
            $dataToUpdate['campaign_id'] = null;
        } else {
            $dataToUpdate['campaign_id'] = $data['campaign_id'];
        }
        

        if (is_numeric($data['contact_id'])) {
            $contactId = $data['contact_id'];
        } else {
            $contactId = null;
        }

        
        $dataToUpdate['contact_id'] = $contactId;

        $date = new Zend_Date($this->_metaData['date']);
        $this->_transactionTime = $date->getTimestamp();
        $dataToUpdate['date'] = $date->getTimestamp();

        $where = $table->getAdapter()->quoteInto('invoice_id = ?',
                                                             $this->_invoiceId);
        $table->update($dataToUpdate, $where);

        $this->getServiceItemModel()->setInvoiceModel($this)->deleteAll();
        
        $serviceItemModel = new Core_Model_Invoice_ServiceItem;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $serviceItemId = $serviceItemModel->create($itemsData[$i], 
                                                            $this->_invoiceId);
        }
        
        $invoiceData = $this->fetch();
        $ledgerEntryIds = unserialize($invoiceData['s_ledger_entry_ids']);
        $result = $this->getLedgerEntryModel()->deleteByIds($ledgerEntryIds);
       
        
        if ($toTypeName == "account")  {
            $accountModel =  new Core_Model_Account($data['account_id']);
            $this->_invoiceTo = 'Type = Account  Name = '.
                                                $accountModel->getName();
            $this->ledgerEntries($accountModel->getLedgerId());
        }
        
        if ($toTypeName == "contact") {
            $contactModel =  new Core_Model_Contact($data['contact_id']);
            $this->_invoiceTo = 'Type = Contact  Name = '.
                                                $contactModel->getFullName();
            $this->ledgerEntries($contactModel->getLedgerId());
        }
    }    
}


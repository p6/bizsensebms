<?php
/*
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

/** @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Quote extends Core_Model_Abstract
{
    protected $_quoteId;    
    
   
    const TO_TYPE_ACCOUNT = 1;
    const TO_TYPE_CONTACT = 2;
    
    const QUOTE_ITEM = 'Quote item';
    const QUOTE_ITEM_TYPE_SERVICE = 1;
    const QUOTE_ITEM_TYPE_PRODUCT = 2;
        
    public function __construct($quoteId = null)
    {
        parent::__construct();
        if (is_numeric($quoteId)) {
            $this->_quoteId = $quoteId;
        }
        
    }
    
    /**
     * @var object the quote item model
     */
    protected $_itemModel;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Quote';
    
    /**
     * @return int the quote id
     */
    public function getQuoteId()
    {
        return $this->_quoteId;
    }
    
    /**
     * @param int $quoteId the invoice ID
     * @return object Core_Model_Quote
     */
    public function setQuoteId($quoteId)
    {
        $this->_quoteId = $quoteId;
        return $this;
    }
    
    /**
     * @return Zend_Db_Select
     * Index of quotes
     */
    public function getIndex()
    {
        $select = $this->db->select();
        $select->from(array('q'=>'quote'), 
                    array('id', 'subject', 'created'))
                ->joinLeft(array('a'=>'account'),
                    'a.accountId = q.accountId', array('a.accountName'))
                ->joinLeft(array('c'=>'contact'),
                    'c.contactId = q.contactId', array('c.firstName'))
                ->joinLeft(array('b'=>'branch'),
                    'b.branchId = q.branchId', array('b.branchName'))
                ->joinLeft(array('u'=>'user'),
                    'u.uid = q.assignedTo', array('u.email'));
            
        return $select;
    }

    public function setId($quoteId)        
    {
        if (is_numeric($quoteId)) {
            $this->_quoteId = $quoteId;
        }    
    }

    
    /**
     * Create a quote
     */
    public function create($itemsData = array(), $data = array())
    {
        if ($data['to_type'] == 1) {
            $toType = Core_Model_Quote::TO_TYPE_ACCOUNT;
            $toTypeId = $data['account_id'];
        } else {
            $toType = Core_Model_Quote::TO_TYPE_CONTACT;
            $toTypeId = $data['contact_id'];
        }

         if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }

        if (is_numeric($data['contact_id'])) {
            $contactId = $data['contact_id'];
        } else {
            $contactId = null;
        }
        
        if (is_numeric($data['account_id'])) {
            $accountId = $data['account_id'];
        } else {
            $accountId = null;
        }
        
        $date = new Zend_Date($data['date']);
       
        $quoteData = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'assigned_to' => $data['assigned_to'],
            'to_type' => $toType,
            'to_type_id' => $toTypeId,
            'contact_id' => $contactId,
            'date' => $date->getTimestamp(),
            'subject' => $data['subject'],
            'description' => $data['description'],
            'delivery_terms' => $data['delivery_terms'],
            'payment_terms' => $data['payment_terms'],
            'internal_notes' => $data['internal_notes'],
            'campaign_id' => $data['campaign_id'],
            'discount_amount' => $data['discount_amount'],
            'quote_status_id' => $data['quote_status_id']
        );
       
        $this->_quoteId = parent::create($quoteData);
        
        $quoteItemModel = new Core_Model_Quote_Item;
        for ($i = 0; $i < count($itemsData); $i++ ) {
            $quoteItemId = $quoteItemModel->create($itemsData[$i], $this->_quoteId);
        }
        
        return $invoiceId;
    }
    
    /**
     * Feteches a record from the quote table
     * @return result object from Zend_Db_Select object
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('quote_id = ?', $this->_quoteId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @return object Core_Model_quote_Item the dependant quote item model
     */
    public function getItemModel()
    {
        if (null === $this->_itemModel) {
            $this->_itemModel = new Core_Model_Quote_Item;
        }
        return $this->_itemModel;
    }
    
    /**
     * @return array the quote items
     */
    public function getItems()
    {
        $table = $this->getItemModel()->getTable();
        $select = $table->select();
        $select->where('quote_id = ?', $this->_quoteId);
        $rowset = $table->fetchAll($select);
        return $rowset->toArray();
    }

    /**
     * @return float the quote total amount
     */
    public function getTotalAmount()
    {
        $quoteRecord = $this->fetch();
        $quoteItems = $this->getItems();
        $total = 0;
        foreach ($quoteItems as $item) {
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
        if ($quoteRecord['discount_amount'] != 0 || 
                                $quoteRecord['discount_amount'] != null) {
            $total -= $quoteRecord['discount_amount'];
        }
        return $total;
    }
    
    /**
    * Edit the quote
    * @param array $data the meta data of the invoice
    * @param array $itemsData the invoice items that have changed
    */ 
    public function edit($itemsData = array(), $data = array()) 
    {  
        $this->prepareEphemeral();    
        $this->_metaData = $data;
        $this->_itemsData = $itemsData;
        $table = $this->getTable();

        if ($data['to_type'] == 1) {
            $toType = Core_Model_Quote::TO_TYPE_ACCOUNT;
            $toTypeId = $data['account_id'];
        } else {
            $toType = Core_Model_Quote::TO_TYPE_CONTACT;
            $toTypeId = $data['contact_id'];
        }

         if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }

        if (is_numeric($data['contact_id'])) {
            $contactId = $data['contact_id'];
        } else {
            $contactId = null;
        }
        
        if (is_numeric($data['account_id'])) {
            $accountId = $data['account_id'];
        } else {
            $accountId = null;
        }
        
        $quoteDataToUpdate = array(
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId(),
            'branch_id' => $data['branch_id'],
            'assigned_to' => $data['assigned_to'],
            'to_type' => $toType,
            'to_type_id' => $toTypeId,
            'contact_id' => $contactId,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'delivery_terms' => $data['delivery_terms'],
            'payment_terms' => $data['payment_terms'],
            'internal_notes' => $data['internal_notes'],
            'campaign_id' => $data['campaign_id'],
            'discount_amount' => $data['discount_amount'],
            'quote_status_id' => $data['quote_status_id']
        );
        
        $where = $table->getAdapter()->quoteInto('quote_id = ?', $this->_quoteId);
        $table->update($quoteDataToUpdate, $where);
        
        $this->getItemModel()->setQuoteModel($this)->deleteAll();
        for ($i = 0; $i < count($itemsData); $i++) {
            $itemsData[$i]['quote_id'] = $this->_quoteId;
            $itemsData[$i]['invoice_item_type'] = 1;
            $itemsData[$i]['invoice_item_inventory_id'] = $itemsData[$i]['product_id'];
            $this->getItemModel()->create($itemsData[$i],$this->_quoteId);  
        } 
        
    }    
   
    /**
     *
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
     * Remove the quote from the database
     */
    public function delete()
    {
        $this->prepareEphemeral();  
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto(
            'quote_id = ?', $this->_quoteId
        );
        $result = $table->delete($where);
        return $result;
    }
    /**
     * Initiate index search processing object
     * @return Zend_Db_Select object
     */
    public function getListingSelectObject($search, $sort)
    {
        $process = new Core_Model_Quote_Index;
        $processed = $process->getListingSelectObject($search, $sort);
        return $processed;
    }
    
    public function fetchItemsAsArray()
    {
       $table = $this->getTable();
       $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                   ->setIntegrityCheck(false)
                    ->where('quote_id = ?', $this->_quoteId);
       $result = $table->fetchRow($select);
       return $result;
    }

    /**
     * @return string PDF file location 
     */
    public function getPdfFileLocation()
    {
        $pdf = new Core_Model_Quote_Pdf_Create();
        $pdf->setModel($this);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/quote/pdf/quote_id_' . $this->_quoteId . '.pdf';
        $pdf->Output($pdfPath, 'F');
        return $pdfPath;
    }
    
    public function csvexport()
    {
        $quoteRecord = $this->fetch();
        $quoteItems = $this->getItems();
        
        $file = "";
        $file .= "Quote Id,".$quoteRecord['quote_id'];
        $file .= PHP_EOL; 
        $file .= "Total amount,".$this->getTotalAmount();
        $file .= PHP_EOL; 
        if ($quoteRecord['to_type'] == Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            $accountModel = new Core_Model_Account($quoteRecord['to_type_id']);
            $partyName = $accountModel->getName();
            $file .= "Party type,".'Account ';
            $file .= $partyName;
            $file .= PHP_EOL; 
        } else {
            $contactModel = new Core_Model_Contact($quoteRecord['to_type_id']);
            $partyName = $contactModel->getFullName();
            $file .= "Party type,"."Contact ";
            $file .= $partyName;
            $file .= PHP_EOL; 
        }
        
        if ($quoteRecord['campaign_id'] != '') {
            $campaignModel = new Core_Model_Campaign;
            $campaignModel->setCampaignId($quoteRecord['campaign_id']);
            $campaignRecord = $campaignModel->fetch();
            $file .= "Campaign id,".$campaignRecord['name'];  
            $file .= PHP_EOL;  
        }   
        
        $date = new Zend_Date();
        $date->setTimestamp($quoteRecord['created_by']);
        $file .= "Created On,".$date->toString();
        $file .= PHP_EOL;
        
        $user = new Core_Model_User($quoteRecord['created_by']);
        $file .= "Created By,".$user->getProfile()->getFullName();
        $file .= PHP_EOL;      
        
        $branch = new Core_Model_Branch($quoteRecord['branch_id']);
        $file .= "Branch,".$branch->getName();
        $file .= PHP_EOL;
         
        $file .="Discount Amount,".$quoteRecord['discount_amount'];
        $file .= PHP_EOL;
        
        $file .="Description,".$quoteRecord['description'];
        $file .= PHP_EOL;
        
        $file .="Delivery terms,".$quoteRecord['delivery_terms'];
        $file .= PHP_EOL;
        
        $file .="Payment terms,".$quoteRecord['payment_terms'];
        $file .= PHP_EOL;
        
        $file .="Internal Notes,".$quoteRecord['internal_notes'];
        $file .= PHP_EOL;
        
        $file .= PHP_EOL;
        $file .= PHP_EOL;
        
        $file .= "Serial, Item, Unit Price, Quantity, Tax Type, Tax, Total";
        $file .= PHP_EOL;
        
        for ($i = 0; $i <= sizeof($quoteItems)-1; $i += 1) {
             $file .= PHP_EOL;
             $file .= $quoteItems[$i]['quote_item_id'].',';
             $model = new Core_Model_Product($quoteItems[$i]['quote_item_id']);
             $file .= $model->getName().',';
             $file .= $quoteItems[$i]['unit_price'].',';
             $file .= $quoteItems[$i]['quantity'].',';
             $taxModel = new Core_Model_Tax_Type($quoteItems[$i]['tax_type_id']);
             $file .= $taxModel->getTaxNameFromId($quoteItems[$i]['tax_type_id']);
             $file .= ',';
             $taxPercentage = $taxModel->getPercentage($quoteItems[$i]['tax_type_id']);
             $beforeTax = $quoteItems[$i]['unit_price'] * $quoteItems[$i]['quantity'];
             $tax = ($beforeTax * $taxPercentage) / 100;
             $file .= $tax;
             $file .= ',';
             $file .= $beforeTax + $tax;
        }
        return $file;
    }

    /**
     * @param string $quotePartyEmailAddress
     * @return bool
     */
    public function sendQuoteEmail($quotePartyEmailAddress, $subject, $textBody)
    {
        if (!$quotePartyEmailAddress) {
            return false;
        }
        try {
            $mail = new Core_Service_Mail;
            $pdfPath = $this->getPdfFileLocation();
            $attachment = $mail->createAttachment(file_get_contents($pdfPath));
            $attachment->type = 'application/pdf';
            $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
            $attachment->encoding = Zend_Mime::ENCODING_BASE64;
            $attachment->filename = 'Quote.pdf';

            $mail->setBodyText($textBody);
            $mail->addTo($quotePartyEmailAddress);
            $mail->setSubject($subject);
            $mail->send();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
        
    /**
     * @param int campaignId
     * @return array the quotes record with campaignId
     */
    public function getQuotesByCampaignId($campaignId)
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
     * @return @Zend_Pagintor object
     */
    public function getDateRangePaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_Report_DateRange';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }
    
    /**
     * @return @Zend_Pagintor object
     */
    public function getContactAccountPaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_Report_ContactAccount';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }
    
    /**
     * @return @Zend_Pagintor object
     */
    public function getAssignToPaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_Report_AssignTo';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }
    
    /**
     * @return @Zend_Pagintor object
     */
    public function getStatusPaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_Report_Status';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }


}



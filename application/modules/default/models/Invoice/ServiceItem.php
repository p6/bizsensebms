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

class Core_Model_Invoice_ServiceItem extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_InvoiceServiceItem';

    /**
     * @var object the invoice model
     */
    protected $_invoiceModel;
    
   /**
     * @var the service Item ID
     */
    protected $_serviceItemId;

    /**
     * @param serviceItemId
     */
     public function __construct($serviceItemId = null)
     {
        if (is_numeric($serviceItemId)) {  
            $this->_serviceItemId = $serviceItemId;
        }
        parent::__construct();
     }
    /**
     * @param object Core_Model_Invoice
     * @return object Core_Model_Invoice_Item
     */
    public function setInvoiceModel($invoiceModel)
    {
        $this->_invoiceModel = $invoiceModel;
        return $this;
    }

    /**
     * @param int Service Item Id
     * @return fluent interface
     */
    public function setServiceItemId($serviceItemId)
    {
        $this->_serviceItemId = $serviceItemId;
        return $this;
    }

    /**
     * @return int the Service Item ID
     */
    public function getserviceItemId()
    {
        return $this->_serviceItemId;
    }
    
    /**
     * Create a finance Purchase Item
     * @param array $data with keys and purchase id
     * @return int Purchase Item ID 
     */
    public function create($data = array(), $invoiceId)
    {
        $dataToinsert['service_item_id'] = $data['product_id'];
        $dataToinsert['description'] = $data['item_description'];
        $dataToinsert['amount'] = $data['unit_price'];
        $dataToinsert['tax_type_id'] = $data['tax_type_id'];
        $dataToinsert['invoice_id'] = $invoiceId;
        $data['invoice_id'] = $invoiceId;
        if ($data['tax_type_id'] == '') {
            $dataToinsert['tax_type_id'] = null;
        } 
        return parent::create($dataToinsert);
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
                    ->where('service_item_id = ?', $this->_serviceItemId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * Delete all the items of the given invoice
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('invoice_id = ?', $this->_invoiceModel->getInvoiceId());
        $table->delete($where);
    }
    
}


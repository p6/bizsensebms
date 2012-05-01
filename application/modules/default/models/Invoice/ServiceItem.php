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


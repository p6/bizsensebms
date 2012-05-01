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
class Core_Model_Finance_Purchase_Item extends Core_Model_Abstract
{
    /**
     * @var the purchaseItem ID
     */
    protected $_purchaseItemId;
    
    const TO_TYPE = 'inoice to type';
    const TO_TYPE_ACCOUNT = 1;
    const TO_TYPE_CONTACT = 2;   

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_PurchaseItem';

    /**
     * @param purchaseItemId
     */
     public function __construct($purchaseItemId = null)
     {
        if (is_numeric($purchaseItemId)) {  
            $this->_purchaseItemId = $purchaseItemId;
        }
        parent::__construct();
     }
     
    /**
     * @var object the purchase model
     */
    protected $_purchaseModel;
    
    /**
     * @param int $purchaseItemId
     * @return fluent interface
     */
    public function setPurchaseItemtId($purchaseItemId)
    {
        $this->_purchaseItemId = $purchaseItemId;
        return $this;
    }

    /**
     * @return int the purchase Item ID
     */
    public function getPurchaseItemId()
    {
        return $this->_purchaseItemId;
    }


    /**
     * Create a finance Purchase Item
     * @param array $data with keys and purchase id
     * @return int Purchase Item ID 
     */
    public function create($data = array(),$purchaseId)
    {
        $data['purhcase_id'] = $purchaseId;
        if ($data['tax_type_id'] == '') {
            $data['tax_type_id'] = null;
        } 
        return parent::create($data);
    }
    
    /**
     * @param array $data with keys and purchaseId
     * @return bool
     */
    public function edit($data,$purchaseId)
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('receipt_id = ?', $purchaseId);
        return $table->update($data, $where);
    }
    
    /**
     * @param object Core_Model_Purchase
     * @return object Core_Model_PurchaseItem
     */
    public function setPurchaseModel($purchaseModel)
    {
        $this->_purchaseModel = $purchaseModel;
        return $this;
    }

    /**
     * Delete all the items of the given purchase Item
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('purhcase_id = ?', 
                                        $this->_purchaseModel->getPurchaseId());
        $table->delete($where);
    }

}

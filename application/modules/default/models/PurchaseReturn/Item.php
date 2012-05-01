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

class Core_Model_PurchaseReturn_Item extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_PurchaseReturnItem';

    /**
     * @var object the purchaseReturn model
     */
    protected $_purchaseReturnModel;

    /**
     * @param object Core_Model_purchaseReturn
     * @return object Core_Model_purchaseReturn_Item
     */
    public function setPurchaseReturnModel($purchaseReturnModel)
    {
        $this->_purchaseReturnModel = $purchaseReturnModel;
        return $this;
    }
    
    /**
     * Create a purchase Rreturn record
     * @param array $data with keys 
     * @return int ledger ID 
     */
    public function create($data = array(),$purchaseReturnId)
    {
        $data['purchase_return_id'] = $purchaseReturnId;
        return parent::create($data);
    }
    
    /**
     * @param array $data and purchase Return Id
     * @return bool
     */
    public function edit($data,$purchaseReturnId)
    {
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('purchase_return_id = ?', $purchaseReturnId);
        return $table->update($data, $where);
    }
    
    /**
     * Delete all the items of the given purchaseReturn
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('purchase_return_id = ?', $this->_purchaseReturnModel->getpurchaseReturnId());
        $table->delete($where);
    }


}


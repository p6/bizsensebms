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

class Core_Model_SalesReturn_Item extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_SalesReturnItem';

    /**
     * @var object the salesReturn model
     */
    protected $_salesReturnModel;

    /**
     * @param object Core_Model_salesReturn
     * @return object Core_Model_salesReturn_Item
     */
    public function setSalesReturnModel($salesReturnModel)
    {
        $this->_salesReturnModel = $salesReturnModel;
        return $this;
    }
    
    /**
     * Create a Sales Rreturn record
     * @param array $data with keys 
     * @return int ledger ID 
     */
    public function create($data = array(),$salesReturnId)
    {
        $data['sales_return_id'] = $salesReturnId;
        return parent::create($data);
    }
    
    /**
     * @param array $data and Sales Return Id
     * @return bool
     */
    public function edit($data,$salesReturnId)
    {
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('sales_return_id = ?', $salesReturnId);
        return $table->update($data, $where);
    }
    
    /**
     * Delete all the items of the given salesReturn
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('sales_return_id = ?', $this->_salesReturnModel->getSalesReturnId());
        $table->delete($where);
    }


}


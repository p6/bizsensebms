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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Product extends Core_Model_Abstract
{
   
    /**
     * The service item id
     */
    protected $_productId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Product';
 
    /**
     * @param service product id
     */
     public function __construct($productId = null)
     {
        if (is_numeric($productId)) {  
            $this->_productId = $productId;
        }
        parent::__construct();
     }
     
    public function setId($productId)
    {
        if (is_numeric($productId)) {  
            $this->_productId = $productId;
        }
        return $this;
    }


    /**
     * Feteches a record from the service table
     * @return result object from Zend_Db_Select object
     */
    public function fetch()
    {
        $serviceId = $this->_productId;
        $table = $this->getTable();
        $select = $table->select();
        $select->where('product_id = ?', $serviceId);
        $result = $table->fetchRow($select);
        return $result;
    }

    /**
     * Fetch all records from persistance 
     * @return Zend_Db_Table_Rowset object
     */
    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select();
        $result = $table->fetchAll($select);
        return $result;
    }


    /**
     * @return string the service item name
     */
    public function getName()
    {
        $result = $this->fetch();
        if ($result) {
            $serviceItemData = $result->toArray();
            $name = $serviceItemData['name'];
        } else {
            $name = '';
        }
        return $name;
    }

   /**
    * Updates the row in the service table
    */ 
    public function edit($data = array()) 
    {
        $serviceId = $this->_productId;
        $table = $this->getTable();
        $data = $this->unsetNonTableFields($data);
        $where = $table->getAdapter()->quoteInto('product_id = ?', $serviceId);
        $result = $table->update($data, $where);
        return $result;
    }    

    /** 
     * Deletes a row in the service table
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('product_id = ?', $this->_productId);
        $result = $table->delete($where);
        return $result;
    }

    /**
     * Set properties of the general service item
     */
    public function setGeneralProperties($data = array())
    {
        
        $validator = new Core_Model_Product_Validate_GeneralPropertyExists;
        $isValid = $validator->isValid($this->_productId);
      
        $table = new Core_Model_DbTable_ProductGeneral;
        $fields = $table->info(Zend_Db_Table_Abstract::COLS);
        $data['product_id'] = $this->_productId;
        foreach ($data as $field => $value) {
            if (!in_array($field, $fields)) {
                unset($data[$field]);
            }
        }

        if ($isValid) {
            $where = $table->getAdapter()->quoteInto('product_id = ?', $this->_productId);
            $table->update($data, $where);
        } else {
            $table->insert($data);
        }
    }

    /**
     * Feteches a record from the service table
     * @return result object from Zend_Db_Select object
     */
    public function fetchAllActiveProducts()
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where('active = ?', '1');
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        
        return $result;
    }

}


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

class Core_Model_ServiceItem extends Core_Model_Abstract
{
   
    /**
     * The service item id
     */
    protected $_serviceProductId;

    /**
     * @param serviceProductId
     */
     public function __construct($serviceProductId = null)
     {
        if (is_numeric($serviceProductId)) {  
            $this->_serviceProductId = $serviceProductId;
        }
        parent::__construct();
     }
     
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_ServiceProduct';
 

    /**
     * @param int serviceProductId
     * @return fluent interface
     */
    public function setServiceProductId($serviceProductId)
    {
        $this->_serviceProductId = $serviceProductId;
        return $this;
    }

    /**
     * @return int the service product id
     */
    public function getServiceProductId()
    {
        return $this->_serviceProductId;
    }

    /**
     * Create a service product
     * @param array $data with keys
     * @return int service product ID 
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $dataToInsert = array(
            'name' => $data['name'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'active' => $data['active']
        );
        if ($data['tax_type_id']) {
           $dataToInsert['tax_type_id']  = $data['tax_type_id'];
        }
        $this->_serviceProductId = $table->insert($dataToInsert);
        
        return $this->_serviceProductId;
    }
    
    /**
     * Fetches a single service product record from db 
     * Based on currently set serviceProductId
     * @return array of service product record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('service_item_id = ?', $this->_serviceProductId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param array $data with keys
     * updates service product details
     * @return int number of rows affected
     */
    public function edit($data)
    {
        $table = $this->getTable();
        $dataToUpdate = array(
            'name' => $data['name'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'active' => $data['active']
        );
        if ($data['tax_type_id']) {
           $dataToInsert['tax_type_id']  = $data['tax_type_id'];
        }
        $where = $table->getAdapter()->quoteInto('service_item_id   = ?', 
                                                   $this->_serviceProductId);
        $result = $table->update($dataToUpdate, $where);
        
        return $result;
    }
    
    /**
     * @return string name
     */
    public function getName()
    {
       $data = $this->fetch();
       return $data['name'];
    }
    
    /**
     * deletes a row in table based on currently set serviceProductId
     * @return int number of rows deleted
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('service_item_id   = ?', 
                                                  $this->_serviceProductId);
        $result = $table->delete($where);
        return $result;
    }
    
    /**
     * Feteches a record from the service table
     * @return result object from Zend_Db_Select object
     */
    public function fetchAllActiveItems()
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

   

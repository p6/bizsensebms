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

   

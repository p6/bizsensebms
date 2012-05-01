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


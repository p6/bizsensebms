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
 * an electronic mail 
 * to info@binaryvibes.co.in
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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Finance_Account_Account extends Core_Model_Abstract
    implements Core_Model_Finance_Account_Interface
{

    /**
     * @var string Zend_Db_Table_Abstract class name
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_Account_Account';

    /**
     * @var The contact id
     */
    protected $_account_id;

    /**
     * The financial account row id
     */
    protected $_fa_account_id;


    /**
     * @param int $id the id of the financial account row
     */
    public function setId($id)
    {
        $this->_fa_account_id = $id;
    }

    /**
     * Set the contact id
     */
    public function setAccountId($id)
    {
        $this->_account_id = $id;
        return $this;
    }
    
    /**
     * Fetch a single recrod from the persistance
     */
    public function fetch()
    {
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
     * Create a debit entry 
     * @return the id of the entry
     */
    public function createDebit($data = array())
    {
        return parent::create($data);
    }

    /**
     * Create a debit entry 
     * @return the id of the entry
     */
    public function createCredit($data = array())
    {
    }

    /**
     * Delete the debit entry
     */
    public function deleteDebit()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto(
            'fa_account_id = ?', $this->_fa_account_id
        );
        $table->delete($where);
    }

    /**
     * Edit a debit entry
     * @param array $data
     */
    public function editDebit($data)
    {
        if (!is_numeric($this->_fa_account_id)) {
            throw new Exception('Financial account ID not set');
        }

        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('fa_account_id = ?', $this->_fa_account_id);
        $table->update($data, $where);
    }

   /**
     * Edit a debit entry
     * @param array $data
     */
    public function saveDebit($data)
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where('fa_account_id = ?', $this->_fa_account_id);
        $exists = $table->fetchRow($select);
        if ($exists) {
            return $this->editDebit($data);
        } else {
            return $this->createDebit($data);
        }
    }


    public function deleteIfFound($id)
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('fa_account_id = ?', $id);
        $table->delete($where);
    }


}


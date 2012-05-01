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

class Core_Model_Finance_Account_Contact extends Core_Model_Abstract
    implements Core_Model_Finance_Account_Interface
{

    /**
     * @var string Zend_Db_Table_Abstract class name
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_Account_Contact';

    /**
     * @var The contact id
     */
    protected $_contact_id;
    
    /**
     * The financial account row id
     */
    protected $_fa_contact_id;


    /**
     * @param int $id the id of the financial account row
     */
    public function setId($id)
    {
        $this->_fa_contact_id = $id;
    }

    /**
     * Set the contact id
     */
    public function setContactId($id)
    {
        $this->_contact_id = $id;
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
            'fa_contact_id = ?', $this->_fa_contact_id
        );
        $table->delete($where);
    }

    /**
     * Edit a debit entry
     * @param array $data
     */
    public function editDebit($data)
    {
        if (!is_numeric($this->_fa_contact_id)) {
            throw new Exception('Financial account ID not set');
        }

        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('fa_contact_id = ?', 
                                                         $this->_fa_contact_id);
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
        $select->where('fa_contact_id = ?', $this->_fa_contact_id);
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
        $where = $table->getAdapter()->quoteInto('fa_contact_id = ?', $id);
        $table->delete($where);
    }


}


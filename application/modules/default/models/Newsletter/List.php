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
class Core_Model_Newsletter_List extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const STATUS_CREATE = 'CREATE';
    const SHOW_IN_CUSTOMER_PORTAL = 1;
    const HIDE_IN_CUSTOMER_PORTAL = 0;
    
    /**
     * The list id
     */
    protected $_listId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_List';

    /**
     * @var object core model lst subscriber 
     */
    protected $_subscriber;
    /*
     * @param int $listId 
     * @return fluent interface    
     */
    public function setListId($listId)
    {
        if (is_numeric($listId)) {
            $this->_listId = $listId;
        }
        return $this;
    }

    /**
     * return listId
     */
   public function getListId()
   {
        return $this->_listId;
   }
    /**
     * @return array the list record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('list_id = ?', $this->_listId);
        $result = $table->fetchRow($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
 
    /*
     * Inserts a record in the list table
     * @param $campaignData form input
     */
    public function create($listData = array())
    {
        $listData['created'] = time();
        $table = $this->getTable();

        $currentUserId = $this->getCurrentUser()->getUserId();
        if (!$currentUserId) {  
            $currentUserId = null;
        }
        $listData['created_by'] = $currentUserId;
        

        $listId = $table->insert($listData);
        $this->_listId = $listId;
        $this->setStatus(self::STATUS_CREATE);
        return $listId;
    }

    /**
     * Updates the row in the Campaign table
     * @param array $data
     * @return int campaign ID
     */ 
    public function edit($data = array()) 
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('list_id = ?', $this->_listId);
        $result = $table->update($data, $where);
        return $result;
    }    

    /** 
     * Deletes a row in the campaign table
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('list_id = ?', $this->_listId);
        $result = $table->delete($where);
        return $result;
    }

    public function getSubscriber()
    {
        if (!$this->_subscriber) {
           $this->_subscriber = new Core_Model_Newsletter_Subscriber;
           $this->_subscriber->setModel($this);
        }
        return $this->_subscriber;
    }

    /**
     * @param array $criteria 
     *  criteria keys
     *      show_in_customer_portal
     */
    public function fetchAll($criteria = array())
    {
        $table = $this->getTable();
        $select = $table->select();
        if (is_array($criteria)) {
            if (isset($criteria['show_in_customer_portal']) and 
                $criteria['show_in_customer_portal'] === self::SHOW_IN_CUSTOMER_PORTAL
            ) {
                $select->where('show_in_customer_portal = ?', self::SHOW_IN_CUSTOMER_PORTAL);
            }
        }
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @param int ListId
     * @return bool
     */
    public function shownInCustomerPortal($listId)
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('list_id = ?', $listId);
        $result = $table->fetchRow($select);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}

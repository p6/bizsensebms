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
class Core_Model_Newsletter_List_Subscriber extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const STATUS_CREATE = 'CREATE';

    const FORMAT_HTML = 1;
    const FORMAT_TEXT = 0;

    const MESSAGE_HTML = 'HTML';
    const MESSAGE_TEXT = 'TEXT';

    /**
     * The service subscriber id
     */
    protected $_listSubscriberId;

    /**
     * @var list model
     */
    protected $_model;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_ListSubscriber';

    /*
     * @param int $listSubscriberId 
     * @return fluent interface    
     */

    public function setListSubscriberId($listSubscriberId)
    {
        if (is_numeric($listSubscriberId)) {
            $this->_listSubscriberId = $listSubscriberId;
        }
        return $this;
    }
   
    /**
     * @return array the list_subscriber record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('list_subscriber_id = ?', $this->_listSubscriberId);
        $result = $table->fetchRow($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
 
    /*
     * Inserts a record in the list_subscriber table
     * @param $listId and $subscriberId form input
     */
    public function create($listId, $subscriberId)
    {
        $data['list_id'] = $listId;
        $data['subscriber_id'] = $subscriberId;
        $table = $this->getTable();
        $this->_listSubscriberId = $table->insert($data);        
        return $this->_listSubscriberId;
    }

    /**
     * Updates the row in the list_subscriber table
     * @param array $data
     * @return int subscriber ID
     */ 
    public function edit($data = array()) 
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('list_subscriber_id = ?', $this->_listSubscriberId);
        $result = $table->update($data, $where);
        return $result;
    }    

    /** 
     * Delete a row in the list_subscriber table
     */
    public function deleteByListIdAndSubscriberId($listId, $subscriberId)
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('subscriber_id = ?', $subscriberId);
        $where .= " AND ".$table->getAdapter()->quoteInto('list_id = ?', $listId);
        $result = $table->delete($where);
        return $result;
    }
    
    /** 
     * @param int listId
     * @return array 
     */
    public function getSubscribersByListId($listId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('list_id = ?', $listId);
        $result = $table->fetchall($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
    
    /** 
     * @param int subscriberId
     * @return array 
     */
    public function getListBySubscribersId($subscriberId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('subscriber_id = ?', $subscriberId);
        $result = $table->fetchall($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
    
    /** 
     * @param int listId and subscriberId
     * @return array 
     */
    public function getByListIdAndSubscriberId($listId, $subscriberId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('subscriber_id = ?', $subscriberId)
                        ->where('list_id = ?', $listId);
        $result = $table->fetchall($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
    
    /** 
     * Delete a row in the list_subscriber table
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('list_subscriber_id = ?', $this->_listSubscriberId);
        $result = $table->delete($where);
        return $result;
    }
    
    /** 
     * @param hash
     * Delete a row in the list_subscriber table
     */
    public function unsubscribe($hash)
    {
        $queueModel = new Core_Model_Newsletter_Message_Queue;
        $data = $queueModel->fetchByHash($hash);
        $result = "";
        if (isset($data['list_id']) && isset($data['subscriber_id'])) {
            $result = $this->deleteByListIdAndSubscriberId($data['list_id'], $data['subscriber_id']);
        }
        return $result;
    }
        
}

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

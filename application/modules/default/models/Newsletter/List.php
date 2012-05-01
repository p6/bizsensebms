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

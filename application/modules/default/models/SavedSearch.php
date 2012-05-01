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
class Core_Model_SavedSearch extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const TYPE_LEAD = '1';
    const TYPE_OPPORTUNITY = '2';
    const TYPE_ACCOUNT = '3';
    const TYPE_CONTACT = '4';

    /**
     * @var list model
     */
    protected $_model;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_SavedSearch';

    /**
     * @var int saved search Id
     */
    protected $_savedSearchId;
    
    /**
     * @param int $savedSearchId
     * @return fluent interface
     */
    public function setSavedSearchId($savedSearchId)
    {
        $this->_savedSearchId = $savedSearchId;
        return $this;
    }

    /**
     * @return int saved search ID
     */
    public function getSavedSearchId()
    {
        return $this->_savedSearchId;
    }

    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('saved_search_id = ?', $this->_savedSearchId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }


    /*
     * Inserts a record in the saved_search table
     * @param $saveId form input
     */
    public function create($name, $data = array(), $type)
    {
        $table = $this->getTable();
        $search['name'] = $name;
        $search['type'] = $type;
        
        $serializedData = serialize($data);
        $data['created'] = time();
        $data['created_by'] = $this->getCurrentUser()->getUserId();

        $search['s_criteria'] = $serializedData;
        $search['created'] = $data['created'];
        $search['created_by'] = $data['created_by'];

        $saveSearchId = $table->insert($search);
        $this->_savedSearchId = $saveSearchId;
        return $saveSearchId;
    }
    
    /**
     * @param int $type
     * @param int $userId
     * @return array rows where type user condition 
     *   match from saved_search record
     */
    public function fetchAll($type, $user)
    {
        $userId = $user->getUserId();
        $table = $this->getTable();
        $select = $table->select();
        $select->where('type = ?', $type)
               ->where('created_by = ?', $userId);
        $result = $table->fetchAll($select);
        if ($result) {
            return $result->toArray();
        }
        return $result; 
    }

    /**
     * @return int the number or records deleted
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('saved_search_id = ?', $this->_savedSearchId);
        $result = $table->delete($where);
        return $result;
    }

    /**
     * @param int type the saved search type
     * @return string name
     */
    public function getNameByType($type)
    {
        if ($type == self::TYPE_LEAD) {
            return 'lead';
        } else if ($type == self::TYPE_OPPORTUNITY) {
            return 'opportunity';
        }  else if ($type == self::TYPE_CONTACT) {
            return 'contact';
        }  else if ($type == self::TYPE_ACCOUNT) {
            return 'account';
        }
    }

    public function getType()
    {
        $row = $this->fetch();
        return $row['type'];
                
    }
}

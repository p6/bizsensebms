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
class Core_Model_Newsletter_DomainBlacklist extends Core_Model_Abstract
{
    /**
     * The domain id
     */
    protected $_domainBlacklistId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Newsletter_DomainBlacklist';

    /*
     * @param int $listId 
     * @return fluent interface    
     */
    public function setDomainBlacklistId($domainBlacklistId)
    {
        if (is_numeric($domainBlacklistId)) {
            $this->_domainBlacklistId = $domainBlacklistId;
        }
        return $this;
    }

    /**
     * return listId
     */
   public function getDomainBlacklistId()
   {
        return $this->_domainBlacklistId;
   }
    /**
     * @return array the domains blacklist record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('domain_blacklist_id = ?', 
                                                $this->_domainBlacklistId);
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
    public function create($domainBlacklistData = array())
    {
        $table = $this->getTable();
        $this->_domainBlacklistId = $table->insert($domainBlacklistData);
        return $this->_domainBlacklistId;
    }

    /**
     * Updates the row in the Campaign table
     * @param array $data
     * @return int campaign ID
     */ 
    public function edit($data = array()) 
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('domain_blacklist_id = ?', 
                                                    $this->_domainBlacklistId);
        $result = $table->update($data, $where);
        return $result;
    }    

    /** 
     * Deletes a row in the campaign table
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('domain_blacklist_id = ?', 
                                                    $this->_domainBlacklistId);
        $result = $table->delete($where);
        return $result;
    }
    
}

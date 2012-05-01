<?php
/*
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
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_UrlAccess extends Core_Model_Abstract 
{
    /**
     * Database table Zend_Db_Table object
     */
    protected $_table;

    /**
     * Table class name
     */
    protected $_dbTableClass = 'Core_Model_DbTable_UrlAccess';

    /** 
     * @param array $record
     * Keys - page - URL to be protected no leading and trailing slashes
     *      - privilege - privilege name
     *      - assertion_class - the assertion class name
     */
    public function insertByPrivilegeName($record)
    {   
        $table = $this->getTable();
        $toInsert = array(
            'url' => $record['url'],
            'privilege_name' => $record['privilege'],
            'assertion_class' => $record['assertion_class']
        );  
        $pageAccessId = $table->insert($toInsert);
        return $pageAccessId;
    }


    /**
     * @param string $url 
     * @return string privilege name
     */
    public function getPrivilgeNameFromUrl($url)
    {   
        $table = $this->getTable();
        $select = $table->select();
        $select->where('url = ?', $url);

        $cache = $this->getCache();

        $cacheId = md5('Core_Model_UrlAccess_' . $url); 
        if (!$result = $cache->load($cacheId)) {
            $result = $table->fetchRow($select);
            $cache->save($result, $cacheId);
        }


        if ($result) {
            $result = $result->toArray();
            $privilegeName = $result['privilege_name'];
            return $privilegeName;
        }
    }

    /**
     * @param string $url
     * @return string assertion class name
     */
    public function getAssertionClass($url)
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where('url = ?', $url);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
            return $result['assertion_class'];
        }
        return;
    }

 


}

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
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Resource extends Core_Model_Abstract 
{
    /**
     * Database table Zend_Db_Table object
     */
    protected $_table;

    /**
     * Table class name
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Resource';

    /**
     * The resource id of the ACL
     */
    protected $_resource_id;

    /**
     * @return object Core_Model_Resource
     * @param int $resourceId the resource id to operate on
     */
    public function setId($resourceId)
    {
        $this->_resource_id = $resourceId;
        return $this;
    }

    /**
     * Create a resource
     * @param array $data contains resource name and description
     * @return last insert id 
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $newData = array(
            'name'=>$data[0], 
            'description'=>$data[1],
        );
        $newData = $this->unsetNonTableFields($newData);
        return $table->insert($newData);          
    }
         
    /*
     * @param $resourceName is the name of the resource
     * @return resource id of the name supplied
     */
    public function getIdFromName($resourceName)
    {
        $table = $this->getTable();
        $select = $table->select()->where('name = ?', $resourceName);
        $result = $table->fetchRow($select)->toArray();
        $this->_resource_id = $result['resource_id'];
        return $result['resource_id']; 
    }
    
     
    public function fetchAll($filter = null)
    {
        $table = $this->getTable();
        $select = $table->select();
        if (is_numeric($filter)) {
            $select->where('resource_id = ?', $filter);
        }
        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    /**
     * Fetch one resource record
     * @return array record of resource table
     */
    public function fetch()
    {

        $cache = Zend_Registry::get('cache');
        $cacheId = md5('aclresourceid' . $this->_resource_id);
        
        if ( !($row = $cache->load($cacheId)) ) {
        
            $table = $this->getTable();
            $rowset = $table->find($this->_resource_id)->toArray();                
            if (is_array($rowset) and isset($rowset[0])) {
                $row = $rowset[0];
            } else {
                $row = null;
            }
            $cache->save($row, $cacheId, array('acl'));
        }

        return $row;
    } 


}

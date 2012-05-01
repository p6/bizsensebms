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

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
class Core_Model_Role extends Core_Model_Abstract
{

    protected $_dbTableClass = 'Core_Model_DbTable_Role';

    /**
     * The role id
     */
    protected $_roleId;



    public function __construct($roleId = null)
    {
        if (is_numeric($roleId)) {
            $this->_roleId = $roleId;
        }
        parent::__construct();
    }

    /**
     * @return array of returned row
     */   
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where('role_id = ?', $this->_roleId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;

    }

    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select();
        $result = $table->fetchAll($select);
        if ($result) {
            $rows = $result->toArray();
            return $rows;
        }
    }

    /**
     * Edit the role information
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('role_id = ?', $this->_roleId);
        $table->update($data, $where);
    }

    /**
     * Delete the role
     */
    public function delete()
    {
        $userRoleModel = new Core_Model_UserRole;
        $result = $userRoleModel->fetchRolesByRoleId($this->_roleId);
        if ($result) {
            return false;
        }
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('role_id = ?', $this->_roleId);
        return $table->delete($where);
    }

}



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
class Core_Model_UserRole extends Core_Model_Abstract 
{
    /**
     * Database table Zend_Db_Table object
     */
    protected $_table;

    /**
     * Table class name
     */
    protected $_dbTableClass = 'Core_Model_DbTable_UserRole';


    /**
     * Add user to role
     * @param array $data contains user id and role name
     * @param $data[0] is the user_id
     * @param $data[1] is the role_id
     * @return last insert id 
     */
    public function addUserToRole($data = array())
    {
        $role = new Core_Model_Role;
        $roleId = $role->getIdFromName($data[1]);
        $table = $this->getTable();
        $newData = array(
            'user_id'   =>  $data[0], 
            'role_id'   =>  $roleId,
        );
        $newData = $this->unsetNonTableFields($newData);
        return $table->insert($newData);          
    }
    
    /**
     * Select user to role
     * @param int user id
     * @return array rows 
     */ 
    public function fetchRoles($user_id)
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from($table)
                ->where('user_id = ?', $user_id)
                ->join('role'
                        ,'role.role_id = user_role.role_id'
                        , 'name');
        $rows = $table->fetchAll($select);  
        if ($rows) {
            $rows = $rows->toArray();
        }
        return $rows;
    }
    
    /**
     * Select users with role
     * @param int role id
     * @return array  
     */ 
    public function fetchRolesByRoleId($role_id)
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from($table)
               ->where('role_id = ?', $role_id);
        $result = $table->fetchAll($select);  
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

}

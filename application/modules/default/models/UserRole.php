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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Acl_Actual extends Zend_Acl
{
    public $db;

    public function __construct()
    {
        $roleModel = new Core_Model_Role;
        $resourceModel = new Core_Model_Resource;
        $privilegeModel = new Core_Model_Privilege;

        $this->db = Zend_Registry::get('db');
        $db = $this->db;
       
        /**
         * add roles
         */
        $sql = 'SELECT name FROM role';
        $result = $db->fetchAll($sql);
        foreach ($result as $value) {
             $this->addRole(new Zend_Acl_Role($value->name));
        }

        /**
         * Add default roles of the application
         */
        $this->addRole(new Zend_Acl_Role('anonymous')); 
        $this->addRole(new Zend_Acl_Role('authenticated')); 
        $this->addRole(new Zend_Acl_Role('superadmin')); 

        /**
         *  add resources 
         */
        $sql = 'SELECT name FROM resource';
        $result = $db->fetchAll($sql);
        foreach ($result as $value) {
            $this->add(new Zend_Acl_Resource($value->name));
        }
        
        /**
         * System default resource
         */
        $this->add(new Zend_Acl_Resource('Auth'));     


       /**
        * Allow resources and privileges to roles
        */
        $sql = 'SELECT role_id, privilege_id FROM access';
        $result = $db->fetchAll($sql);

        foreach ($result as $value) {

            $roleId =  $value->role_id;
            $roleRecord = $roleModel->setId($roleId)->fetch();

            $privilegeId = $value->privilege_id;
            $privilegeRecord = $privilegeModel->setId($privilegeId)->fetch();

            $resourceId = $privilegeModel->setId($privilegeId)->getResourceId();
         #   $resourceId = $db->fetchOne("SELECT resource_id FROM privilege WHERE privilege_id = ?", $privilegeId);
            $resourceRecord = $resourceModel->setId($resourceId)->fetch();

            $this->allow($roleRecord['name'], $resourceRecord['name'], $privilegeRecord['name']);
        }

        /**
         * Allow auth privileges to authenticated user
         * System default privilege for resource Auth
         */
        $this->allow('authenticated', 'Auth', 'auth privileges');

        $this->allow('superadmin');

    }
}



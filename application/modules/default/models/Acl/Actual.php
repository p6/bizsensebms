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



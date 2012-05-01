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

class Core_Model_Acl extends Bare_Acl
{
    
    public function init()
    {
        $privilegeModel = new Core_Model_Privilege;
        $roleModel = new Core_Model_Role;
        $accessModel = new Core_Model_Access;

        $roles = $roleModel->fetchAll();
        foreach ($roles as $role) {
            $this->addRole($role['name']);
        }

        /**
         * Add default roles
         */
        $this->addRole('authenticated');
        $this->addRole('anonymous');
        $this->addRole('superadmin');

        /**
         * Add default privileges
         */
        $this->addPrivilege('auth privileges');

        $this->allow('authenticated', 'auth privileges');

    
        $privileges = $privilegeModel->fetchAll();
        foreach ($privileges as $privilege) {
            $this->addPrivilege($privilege['name']);
        }

        foreach ($roles as $role) {
            $accesses = $accessModel->fetchAllByRoleId($role['role_id']);
            foreach ($accesses as $access) {
                $this->allow($role['name'], $access['privilege_name']);
            }
        }

    }


    /**
     * @see Bare_Acl::isAllowed
     */
    public function isAllowed($roles, $privilege, $assertion = null)
    {
        if (is_object($roles)) {
            if ($roles->getUserId() == 1) {
                return true;
            }
        }
        return parent::isAllowed($roles, $privilege, $assertion);
    }
}



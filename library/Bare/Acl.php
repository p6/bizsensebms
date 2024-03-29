<?php
/**
 * Copyright (c) 2010, Binary Vibes Information Technologies Pvt. Ltd. 
 * (http://binaryvibes.co.in) All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 * 
 * * Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * 
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * * Neither the names of Binary Vibes Information Technologies Pvt. Ltd. 
 *   nor the names of the project contributors may be used to endorse or 
 *   promote products derived from this software without specific prior 
 *   written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF 
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */
class Bare_Acl
{
    /**
     * @var array the role registry
     */
    protected $_roles = array();

    /**
     * @var array the privileges registry
     */
    protected $_privileges = array();

    /**
     * @var array the roles and privileges relationship
     */
    protected $_rolesPrivileges = array();

    /**
     * The roles to which the subject belongs
     */
    protected $_subjectRoles = array();

    /**
     * Add a role to the Bare_Acl role registry
     * @param string $role 
     */
    public function addRole($role)
    {   
        if (in_array($role, $this->getRoles())) {
            throw new Bare_Acl_Exception('Role %s already exists', $role);
        }
        $this->_roles[] = $role;
        $this->_rolesPrivileges[$role] = array();
        return $this;
    }
   
    /**
     * Add roles to the registry
     * @param array $roles
     */
    public function addRoles($roles = array())
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }
    
    /**
     * @return array roles
     */
    public function getRoles()
    {
        return $this->_roles;
    }

    /**
     * Add a privilege to the privilege registry
     * @param string $privilege
     */
    public function addPrivilege($privilege)
    {
        if (in_array($privilege, $this->getPrivileges())) {
            throw new Bare_Acl_Exception('Privilege %s already exists', $role);
        }
        $this->_privileges[] = $privilege;
        return $this;

    }

    /**
     * Add privileges to the registry
     * @param array $privileges
     */
    public function addPrivileges($privileges = array())
    {
        foreach ($privileges as $privilege) {
            $this->addPrivilege($privilege);
        }
        return $this;

    }

    /**
     * @return array privileges
     */
    public function getPrivileges()
    {
        return $this->_privileges;
    }

    public function allow($role, $privilege)
    {
        array_push($this->_rolesPrivileges[$role], $privilege);
        return $this;
    }

    /**
     * @param array|object $roles if object, type Bare_Acl_SubjectInterface
     * @param string $privilege
     * @param object $assert of type Bare_Acl_AssertInterface
     */
    public function isAllowed($roles, $privilege, $assertion = null)
    {
        $allowed = false;

        if (is_object($roles)) {
            $this->setSubject($roles);
        } else if (is_array($roles)) {
            $this->setSubjectRoles($roles);
        }
        if (null != $privilege) {
            if (!in_array($privilege, $this->getPrivileges())) {
                throw new Bare_Exception("The privilege $privilege does not exist");
            }
            foreach ($this->getSubjectRoles() as $role) {
                if ($this->_roleHasPrivilege($role, $privilege)) {
                    $allowed = true;
                    break;
                }
            }
        }

        if (null != $assertion) {
            $allowed = $assertion->assert($this);
        }

        return $allowed;
    }

    /**
     * @param object $subject Bare_Acl_SubjectInterface
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        $this->setSubjectRoles($subject->getRoles());
        return $this;
    }

    /**
     * @return object Bare_Acl_SubjectInterface
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @param array $roles
     */
    public function setSubjectRoles($roles)
    {
        $this->_subjectRoles = $roles; 
        return $this;
    }
   
    /**
     * @return array the roles of the subject
     */
    public function getSubjectRoles()
    {
        return $this->_subjectRoles;                
    }

    /**
     * Determine whether the role has access to the privilege
     * @param string $role
     * @param string $privilege
     * @return bool
     */
    protected function _roleHasPrivilege($role, $privilege)
    {
        if (!in_array($role, $this->getRoles())) {
            throw new Bare_Exception("The role $role does not exist");
        }
        $return = in_array($privilege, $this->_rolesPrivileges[$role]);     
        return $return;
    }

}

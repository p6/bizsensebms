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

class Core_Form_User_EditUser extends Core_Form_User_AddUser
{
    /**
     * The database adapter
     */ 
    public $db;
    
    
    /**
     * The user id
     */
    protected $_userId;

    /**
     * The user object
     */
    protected $_user;

    /**
     * Set the user id
     */
    public function __construct($userId = null)
    {
        $this->db = Zend_Registry::get('db');
        $this->_userId = $userId;
        $user= new Core_Model_User($this->_userId);
        $this->_user = $user->fetch();    
        parent::__construct();
    }

    public function init()
    {
        $db = $this->db;
        $user = $this->_user;
        $uid = $user->user_id;
        $this->setMethod('post');

        $username = $this->createElement('text', 'username')
                        ->setLabel('Email address')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_EmailAddress());

        $email = $db->fetchOne("SELECT email FROM user WHERE user_id = '$uid'");
        $username->setValue($email);

        $submit = $this->createElement('submit', 'Submit',array(
                        'class' => 'submit_button'
                    ));

        $status = $this->createElement('checkbox', 'status')
                            ->setLabel('Status')
                            ->setValue($user->status);
        $this->addElements(array($username, $status));

        $roleform = new Zend_Form_SubForm;
        $roleform->setIsArray(true);
        $sql = "SELECT role_id, name FROM role WHERE role_id";
        $result = $db->fetchAll($sql, array(), Zend_Db::FETCH_ASSOC);

        foreach ($result as $row) {
            $roleId = $row['role_id'];
            $roleName = $row['name'];

            $role{$roleId} = $roleform->createElement('checkbox', $roleId)
                  ->setLabel($roleName);
            $belongsToRole = $db->fetchRow("SELECT user_id FROM user_role where user_id = $uid and role_id = $roleId");
            /*
             * belongsToRole returns true if user belongs to role otherwise boolean false is returned
             */
            if ($belongsToRole) {
                $role{$roleId}->setChecked(true);
            }


            $roleform->addElement($role{$roleId});
        }

        $this->addSubForm($roleform, 'role');
        $this->addElements(array($submit));


    }

}

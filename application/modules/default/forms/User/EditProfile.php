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

class Core_Form_User_EditProfile extends Core_Form_User_AddUser
{
    /**
     * The database adapter
     */
    public $db;

    /**
     * The user id
     */
    protected $_user_id;

    /**
     * Set the user id
     */
    public function __construct($userId = null)
    {
        $this->db = Zend_Registry::get('db');

        if (is_numeric($userId)){
            $this->_user_id = $userId;
        }
   
        parent::__construct(); 
    }

    public function init()
    {
        parent::init();
        $this->setAction('/user/editprofile/user_id/' . $this->_user_id);
        $password = $this->getElement('password')->setRequired(false);
        $password->setDescription('Specifiy only if the password has to be updated. Leave blank otherwise');
        $confirmPassword = $this->getElement('confirm_password')->setRequired(false);

        $user = new Core_Model_User($this->_user_id);
        $userData = $user->fetch();
        $userDataArray = (array) $userData;   
        if ($userData->user_id == 1) {
            $this->getElement('primary_role')->setRequired(false);
        }
        
        $username = $this->createElement('text', 'username')
                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setRequired(true)
                        ->setLabel('Username')
                        ->addValidator(new Zend_Validate_Db_NoRecordExists(
                           'user', 'username',array(
                           'field' => 'username', 'value' => $userData->username)));
                        
        
        $email = $this->createElement('text', 'email')
                            ->setLabel('Email address')
                            ->setRequired(true)
                            ->addValidator(new Zend_Validate_EmailAddress())
                            ->addValidator(new Core_Model_User_Validate_UniqueUserEmail())
                            ->addValidator(new Zend_Validate_Db_NoRecordExists(
                                'user', 'email',array(
                                'field' => 'email', 'value' => $userData->email)));
        $this->addElements(array($username, $email));             
        $this->populate($userDataArray);
                
        $this->removeElement('notify_user');
        $this->getElement('email')->setValue($userData->email);
        $this->getElement('email')->removeValidator('Zend_Validate_Db_NoRecordExists');
        $this->getElement('email')->removeValidator('Core_Model_User_Validate_UniqueUserEmail');

        return $this;

    }

}

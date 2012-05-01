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
 */
/**
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

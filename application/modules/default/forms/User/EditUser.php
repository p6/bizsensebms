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
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

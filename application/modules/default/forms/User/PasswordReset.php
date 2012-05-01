<?php
/*
 * Password reset
 *
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
class Core_Form_User_PasswordReset
{
    public $db;

    /*
     * Rouitne constructor
     */
    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    /*
     * Generate form to reset user's password
     */
    public function getForm()
    {
        $form = new Zend_Form;
        $form->setAction('/user/resetpass');
        $form->setMethod('post');


        $newPassword = $form->createElement('password', 'password');
        $newPassword->setLabel('New Password');
        $newPassword->setRequired(true);
        $newPassword->addValidator('StringLength', false, array(6, 20));

        $confirmPassword = $form->createElement('password', 'password_confirm');
        $confirmPassword->setLabel('Confirm New Password');
        $confirmPassword->setRequired(true);
        $confirmPassword->addValidator(new BV_Validate_MatchPasswords());

        $submit = $form->createElement('submit', 'Submit')
                       ->setAttrib('class', 'submit_button');
        $form -> addElements(array($newPassword, $confirmPassword, $submit));

        return $form;

    }

}

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
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_User_ForgotPassword extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        $username = $this->createElement('text', 'username');
        $username->setLabel('Your e-mail address');
        $username->setRequired(true);
        $username->addValidator(new Zend_Validate_EmailAddress());

        $this->addElement('hash', 'no_csrf_foo', array('salt' => 'unique'));

        $captcha = new Zend_Form_Element_Captcha('foo', 
            array(
                'label' => "Please verify you're a human",
                'captcha' => 'Figlet',
                'captchaOptions' => array(
                    'captcha' => 'Figlet',
                    'wordLen' => 6,
                    'timeout' => 300,
                ),
            )
        ); 

        $submit = $this->createElement('submit', 'Submit')
                        ->setAttrib('class', 'submit_button');                       
        $this->addElements(array($username, $captcha, $submit));

    }

}

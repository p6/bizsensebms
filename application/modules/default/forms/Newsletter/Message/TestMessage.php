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

/* @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Newsletter_Message_TestMessage extends Zend_Form
{

    public function init()
    {
        $this->setName('test_message');

        $this->addElement('text', 'first_name', 
            array(
                'label' => 'First name',
                'required' => false,
            )
        );

        $this->addElement('text', 'middle_name', 
            array(
                'label' => 'Middle name',
                'required' => false,
            )
        );

        $this->addElement('text', 'last_name', 
            array(
                'label' => 'Last name',
                'required' => false,
            )
        );


        $recipient = new Zend_Dojo_Form_Element_ValidationTextBox('recipient');
        $recipient->setOptions(
          array(
              'label'          => 'Email address',
              'required'       => true,
              'invalidMessage' => 'Invalid email address',
              'regExp' => '[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}',
          )
        );
        $recipient->addValidator(new Zend_Validate_EmailAddress());
        $this->addElement($recipient);


        $this->addElement('button', 'submit', 
            array(
                'ignore' => true,
                'label' => 'Send',
                'class' => 'submit_button',
                'attribs' => array('onclick'=>'sendTestMessage()'),
            )
        );
    }
}

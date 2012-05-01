<?php
/*
 * BizSense administration email set form
 *
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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */


class Core_Form_Email_TestMail extends Zend_Form
{

    public function init()
    {
        
        $email = $this->createElement('text', 'email')
                        ->setLabel('Email')
                        ->setRequired(true)
                        ->setDescription('An email will be sent to this email address once you submit the form.')
                        ->addValidator(new Zend_Validate_EmailAddress());
        
        $subject = $this->createElement('text', 'subject')    
                            ->setLabel('Subject');
                            
        $message = $this->createElement('textarea', 'message')
                        ->setLabel('Email Message')  
                        ->setAttribs(array('cols'=>'40', 'rows'=>'5'));   
 
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');
                       
        $this->setElementFilters(array('StringTrim'));
        
        $this->addElements(array($email, $subject, $message, $submit));

        
    }
}



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

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Newsletter_Subscriber_Create extends Zend_Form
{
    
    public function init()
    {
        $this->addElement('text', 'first_name', array
            (
                'label' => 'First Name',
                'required' => true,
            )
        ); 

        $this->addElement('text', 'middle_name', array
            (
                'label' => 'Middle Name',
            )
        ); 

        $this->addElement('text', 'last_name', array
            (
                'label' => 'Last Name',
            )
        ); 

        $email = $this->createElement('text', 'email')
                    ->setLabel('Email')
                    ->setRequired(true)
                    ->addValidator(new Zend_Validate_StringLength(0, 320))
                    ->addValidator(new Zend_Validate_Db_NoRecordExists('subscriber', 'email'))
                    ->addValidator(new Zend_Validate_EmailAddress());

        $this->addElement($email);

        $format = $this->createElement('checkbox', 'format')
                            ->setLabel('HTML Format');
        $this->addElement($format);
        
        $status = $this->createElement('select', 'status')
               ->setRequired(true)
               ->setLabel('Status');
        $status->addMultiOption("1", "Confirmed");
        $status->addMultiOption("2", "Unconfirmed");
        $status->addMultiOption("3", "Active");
        $status->addMultiOption("4", "Blocked");
        $this->addElement($status);
        
        $this->addElement('submit', 'submit', array
            (
                'ignore' => true,
                'class' => 'submit_button'
                
            )
        );
        
        $this->setElementFilters(array('StringTrim'));
    }
}

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

class Core_Form_Newsletter_Subscriber_Search extends Zend_Form
{
    public function init()
    {
        $this->setAction('/newsletter/subscriber');
        $this->setMethod('get');
        $this->setName('search');

        $email = $this->createElement('text', 'email');
        $email->setLabel('E-mail');

        $name = $this->createElement('text', 'name');
        $name->setLabel('Name');
        
        $email = $this->createElement('text', 'email');
        $email->setLabel('E-mail');

        $format = $this->createElement('select', 'format')
                       ->setLabel('Format');
        $format->addMultiOption("", "");
        $format->addMultiOption("1", "HTML");
        $format->addMultiOption("0", "Text");
            
        $status = $this->createElement('select', 'status')
                       ->setLabel('Status');
        $status->addMultiOption("", "");
        $status->addMultiOption("1", "Confirmed");
        $status->addMultiOption("2", "Unconfirmed");
        $status->addMultiOption("3", "Active");
        $status->addMultiOption("4", "Blocked");
        
        $this->addElements(array($email,$name,$format,$status));

        $submit = $this->createElement('submit', 'submit')
                        ->setAttrib('class', 'submit_button')
                        ->setLabel('Search');

        $this->addElement($submit);
        new BV_Form_Filter_AddTrimToElements($this);
    }
}

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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 */

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Ticket_Create extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'title', array
            (
                'label' => 'Title',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 250))
                     )
            )
        ); 

        $this->addElement('textarea', 'description', array
            (
                'label' => 'Description',
                'required' => true,
                'attribs' => array(
                    'rows' => 10,
                    'cols' => 60,
                )
            )
        ); 

        $element = new Core_Form_Contact_Element_Contact;
        $contact = $element->getElement();
        $contact->setRequired(true);
        $this->addElement($contact);

        $status = $this->createElement('select', 'ticket_status_id');
        $status->setLabel('Status')
                ->setRequired(true);
        $ticketStatusModel = new Core_Model_Ticket_Status();
        $ticketStatusCollection = $ticketStatusModel->fetchAll();
        foreach ($ticketStatusCollection as $item) {
           $status->addMultiOption($item['ticket_status_id'], $item['name']); 
        }
        $this->addElement($status);

        $this->addElement('submit', 'submit', array
            (
                'ignore' => true,
                'class' => 'submit_button'
            )
        );

    }
}

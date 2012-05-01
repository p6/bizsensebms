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
class Core_Form_Newsletter_Queue_Search extends Zend_Form
{

    public function init()
    {
        $this->setAction('/newsletter/queue/index/');
        $this->setMethod('get');
        $this->setName('search');
        $db = Zend_Registry::get('db'); 
        
        $this->addElement('text', 'subject', 
            array(
                'label' => 'Message Subject',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 500))
                     )
            )
        );
        
        $this->addElement('text', 'domain', 
            array(
                'label' => 'Domain',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 500))
                     )
            )
        );
        
        $listId = $this->createElement('select', 'list_id')
               ->setLabel('List');
        $listId->addMultiOption("", "any");
        $sql = "SELECT name, list_id FROM list";
        $result = $db->fetchAll($sql);

        foreach ($result as $row) {
            $listId->addMultiOption($row->list_id, $row->name);
        }
        $this->addElement($listId);
        
        $status = $this->createElement('select', 'status')
               ->setLabel('Status');
        $status->addMultiOption("", "any");
        $status->addMultiOption(Core_Model_Newsletter_Message_Queue::MESSAGE_NOT_SENT, 'Not sent');
        $status->addMultiOption(Core_Model_Newsletter_Message_Queue::MESSAGE_SENT, 'Sent');
        $this->addElement($status);
        
        $this->addElement('text', 'email', 
            array(
                'label' => 'E-mail',
                'required' => false
            )
        );
        
        $submit = $this->createElement('submit', 'Search')
                      ->setAttrib('class', 'submit_button');
        $this->addElement($submit);
    }
}

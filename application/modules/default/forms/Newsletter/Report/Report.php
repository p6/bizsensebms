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
class Core_Form_Newsletter_Report_Report extends Zend_Form
{

    public function init()
    {
        $this->setAction('/newsletter/report/queuereport/');
        $this->setMethod('get');
        $this->setName('search');
        
        $startDateFrom = new Zend_Dojo_Form_Element_DateTextBox('date_from');
        $startDateFrom->setLabel('Date From');
        $startDateFrom->setFormatLength('long')
                    ->addValidator(new Core_Form_Activity_Task_Validate_DateCompare)
                    ->setInvalidMessage('Invalid date');
        $this->addElement($startDateFrom);
        
        
        $startTime = new Zend_Dojo_Form_Element_TimeTextBox('start_time');
        $startTime->setLabel('Time');
        $this->addElement($startTime);
                
        $startDateTo = new Zend_Dojo_Form_Element_DateTextBox('date_to');
        $startDateTo->setLabel('Date To');
        $startDateTo->setFormatLength('long')
                    ->setInvalidMessage('Invalid date');
        $this->addElement($startDateTo);
                
        $endTime = new Zend_Dojo_Form_Element_TimeTextBox('end_time');
        $endTime->setLabel('Time');
        $this->addElement($endTime);
        
        $status = $this->createElement('select', 'status')
               ->setLabel('Status');
        $status->addMultiOption("", "any");
        $status->addMultiOption(Core_Model_Newsletter_Message_Queue::MESSAGE_NOT_SENT, 'Not sent');
        $status->addMultiOption(Core_Model_Newsletter_Message_Queue::MESSAGE_SENT, 'Sent');
        $this->addElement($status);
        
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
        
        $submit = $this->createElement('submit', 'submit')
                      ->setAttrib('class', 'submit_button');
        $this->addElement($submit);
    }
}

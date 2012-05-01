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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Activity_Task_Create extends Zend_Form
{
    public function init() 
    {
        $currentUser = Zend_Registry::get('user');

        $subject = $this->createElement('text', 'name')
                     ->addValidator(
                                new Zend_Validate_StringLength(0, 200)
                            )
                     ->setLabel('Title')
                     ->setRequired(true);

        $statusForm = new Core_Form_Activity_Task_Element_TaskStatus;
        $taskStatus = $statusForm->getElement();

        $startDate = new Zend_Dojo_Form_Element_DateTextBox('start_date');
        $startDate->setLabel('Start Date');
        $startDate->setFormatLength('short')
                    ->setRequired('true')
                    ->setInvalidMessage('Invalid date');

        $startTime = new Zend_Dojo_Form_Element_TimeTextBox('start_time');
        $startTime->setLabel('Start Time');

        $endDate = new Zend_Dojo_Form_Element_DateTextBox('end_date');
        $endDate->setLabel('End Date');
        $endDate->setFormatLength('short')
            ->setRequired('true')
            ->addValidator(new Core_Form_Activity_Validate_DateTimeCompare)
            ->setInvalidMessage('Invalid date');

        $endTime = new Zend_Dojo_Form_Element_TimeTextBox('end_time');
        $endTime->setLabel('End Time');

        $reminder = new Core_Form_Activity_Element_Reminder;
        $getReminders = $reminder->getElement();

        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);


        $description = $this->createElement('textarea', 'description')
                            ->setLabel('Description');
        $description->addValidator(new Zend_Validate_StringLength(0, 1000));
        $description->setAttribs(array(
                        'cols' => '40',
                        'rows' => '5'
                    ));
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');

        $recurrence = $this->createElement('select', 'recurrence');
        $recurrence->setLabel('Recurrence');

        $recurrence->addMultiOptions(array('0'=>'None', '1'=>'Daily', 
                        '2'=>'Week', '3'=>'Month', '4'=>'Year'));
        $recurrence->setAttrib('onChange', 'displayForm()');

        $daily = $this->createElement('text', 'daily');
        $daily->setLabel('Daily');

        $monthly = $this->createElement('text', 'monthly');
        $monthly->setLabel('Monthly');

        $yearly = $this->createElement('text', 'yearly');
        $yearly->setlabel('Yearly');
                      
        $this->addElements(
                array($subject, $taskStatus, $startDate, $startTime,
                      $endDate, $endTime, $getReminders, $assignedTo,
                      $description, $submit));

        

    }

}

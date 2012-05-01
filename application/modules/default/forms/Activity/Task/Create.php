<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
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

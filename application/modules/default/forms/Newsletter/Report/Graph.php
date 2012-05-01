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
class Core_Form_Newsletter_Report_Graph extends Zend_Form
{

    public function init()
    {
        $this->setAction('/newsletter/report/queuedomains/');
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
                     ->setLabel('Search')
                      ->setAttrib('class', 'submit_button');
        $this->addElement($submit);
    }
}

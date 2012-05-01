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
class Core_Form_Activity_Meeting_Search extends Zend_Form
{
    public function init() 
    {
        $currentUser = Zend_Registry::get('user');

        $title = $this->createElement('text', 'name');
        $title->setLabel('Title');

        $startDateFrom = new Zend_Dojo_Form_Element_DateTextBox('start_date_from');
        $startDateFrom->setLabel('Start Date From');
        $startDateFrom->setFormatLength('long')
                    ->addValidator(new Core_Form_Activity_Task_Validate_DateCompare)
                    ->setInvalidMessage('Invalid date');

        $startDateTo = new Zend_Dojo_Form_Element_DateTextBox('start_date_to');
        $startDateTo->setLabel('Start Date To');
        $startDateTo->setFormatLength('long')
                    ->setInvalidMessage('Invalid date');

        $endDateFrom = new Zend_Dojo_Form_Element_DateTextBox('end_date_from');
        $endDateFrom->setLabel('End Date From');
        $endDateFrom->setFormatLength('long')
            ->setInvalidMessage('Invalid date');

        $endDateTo = new Zend_Dojo_Form_Element_DateTextBox('end_date_to');
        $endDateTo->setLabel('End Date To');
        $endDateTo->setFormatLength('long')
            ->setInvalidMessage('Invalid date');

        $assignedTo = new Zend_Dojo_Form_Element_FilteringSelect('assigned_to');
        $assignedTo->setLabel('Assign to User')
            ->setAutoComplete(true)
            ->setStoreId('userStore')
            ->setStoreType('dojo.data.ItemFileReadStore')
            ->setStoreParams(array('url'=>'/opportunity/assignto'))
            ->setAttrib("searchAttr", "email");

        $statusForm = new Core_Form_Activity_Meeting_Element_MeetingSearchStatus;
        $meetingStatus = $statusForm->getElement();


        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');
                      

        $this->addElements(
                array($title, $startDateFrom, $startDateTo, $endDateFrom, 
                    $endDateTo, $meetingStatus, $assignedTo ,$submit));

       $startDateSearch = $this->addDisplayGroup(array('start_date_from', 'start_date_to'), 'start_date');
        $startDateSearch->getDisplayGroup('start_date')->setLegend('Start Date Search');

        $endDateSearch = $this->addDisplayGroup(array('end_date_from', 'end_date_to'), 'end_date');
        $endDateSearch->getDisplayGroup('end_date')->setLegend('End Date Search');

        $ownerGroup = $this->addDisplayGroup(array('name', 'assigned_to', 
                    'task_status_id','meeting_status_id'), 'assignedGroup');

        $submitGroup = $this->addDisplayGroup(array('submit'), 'submitGroup');

        $lineElements = array(
            );
        foreach ($lineElements as $element) {
            $element->setAttrib('size', '18');
            $element->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
        ));
        }

       $lineElementsWithoutSizeAttrib = array();
        foreach ($lineElementsWithoutSizeAttrib as $element) {
            $element->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
            ));
        }

       $dojoLineItems = array($startDateFrom, $startDateTo, $endDateFrom, $endDateTo);
        foreach ($dojoLineItems as $element) {
            $element->setDecorators(array(
                'DijitElement',
                'Description',
                'Errors',
                array('Label'),
            ));
        }


        $startDateSearch->getDisplayGroup('start_date')->setAttrib('class','search_fieldset_small')
                    ->setLegend('Start date');
        $endDateSearch->getDisplayGroup('end_date')->setAttrib('class','search_fieldset_small')
                    ->setLegend('End date');
    }
}

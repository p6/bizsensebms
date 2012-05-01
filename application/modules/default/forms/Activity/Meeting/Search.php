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

<?php
/*
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Opportunity_Report_Type1 extends Zend_Form
{

    /** 
     * Opportunity report form
     */   

    public function init()
    {
        $this->setAction('/reports/opportunity/daterange');
        $this->setMethod('post');
        $this->setName('opportunityReport');
        
        $createdFrom = new Zend_Dojo_Form_Element_DateTextBox('created_from');
        $createdFrom->setLabel('Between');

        $createdTo = new Zend_Dojo_Form_Element_DateTextBox('created_to');
        $createdTo->setLabel('And');
        
        $expectedCloseDateFrom = new Zend_Dojo_Form_Element_DateTextBox('expected_close_date_from');
        $expectedCloseDateFrom->setLabel('Between');

        $expectedCloseDateTo = new Zend_Dojo_Form_Element_DateTextBox('expected_close_date_to');
        $expectedCloseDateTo->setLabel('And');

        $branchId = new Core_Form_Branch_Element_BranchMultiselect;
        $branchId = $branchId->getElement();

        $assignedTo = new Core_Form_User_Element_User;
        $assignedTo = $assignedTo->getElement();
    
        $submit = $this->createElement('submit', 'submit', array (
                'class' => 'submit_button'
            )
        );
       
        $this->addElements(array($createdFrom, $createdTo, $expectedCloseDateFrom, $expectedCloseDateTo,
            $branchId, $assignedTo, $submit));

        $createdGroup = $this->addDisplayGroup(array('created_from', 'created_to'), 'createdGroup');
        $expectedCloseDateGroup = $this->addDisplayGroup(array('expected_close_date_from', 'expected_close_date_to'), 
            'expectedCloseDateGroup');
        $ownerGroup = $this->addDisplayGroup(array('branch_id', 'assigned_to'), 'assignedGroup');

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

       
        $dojoLineItems = array($createdFrom, $createdTo, $expectedCloseDateFrom, $expectedCloseDateTo);
        foreach ($dojoLineItems as $element) {
            $element->setDecorators(array(
                'DijitElement',
                'Description',
                'Errors',
                array('Label'),
            ));
        }    

        
        $createdGroup->getDisplayGroup('createdGroup')->setAttrib('class','search_fieldset_small')
                    ->setLegend('Created date');
        $expectedCloseDateGroup->getDisplayGroup('expectedCloseDateGroup')->setAttrib('class','search_fieldset_small')
                    ->setLegend('Expected close date');
    }

}


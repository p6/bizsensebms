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
class Core_Form_Lead_Report_Type1 extends Zend_Form
{
    /* Lead report form
     * IN DEVELOPMENT
     * Add validators
     * Add access checks
     */


    public function init()
    {
        $this->setAction('/reports/lead/daterange');
        $this->setMethod('post');
        $this->setName('leadReport');
        
        $converted = new BV_Form_Element_ConvertedMultiselect;
        $converted = $converted->getElement();

        $createdFrom = new Zend_Dojo_Form_Element_DateTextBox('createdFrom');
        $createdFrom->setLabel('Between');

        $createdTo = new Zend_Dojo_Form_Element_DateTextBox('createdTo');
        $createdTo->setLabel('And');
        
        $lastUpdatedFrom = new Zend_Dojo_Form_Element_DateTextBox('lastUpdatedFrom');
        $lastUpdatedFrom->setLabel('Between');

        $lastUpdatedTo = new Zend_Dojo_Form_Element_DateTextBox('lastUpdatedTo');
        $lastUpdatedTo->setLabel('And');

        $branchId = new Core_Form_Branch_Element_BranchMultiselect;
        $branchId = $branchId->getElement();

        $assignedTo = new Core_Form_User_Element_User;
        $assignedTo = $assignedTo->getElement();
    
        $submit = $this->createElement('submit', 'submit', array (
                'class' => 'submit_button'
            )
        );
       
        $this->addElements(array($converted, $createdFrom, $createdTo, $lastUpdatedFrom, $lastUpdatedTo,
            $branchId, $assignedTo, $submit));

        $createdGroup = $this->addDisplayGroup(array('createdFrom', 'createdTo'), 'createdGroup');
        $lastUpdatedGroup = $this->addDisplayGroup(array('lastUpdatedFrom', 'lastUpdatedTo'), 'lastUpdatedGroup');
        $ownerGroup = $this->addDisplayGroup(array('converted', 'branch_id', 'assigned_to'), 'assignedGroup');

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

       
        $dojoLineItems = array($createdFrom, $createdTo, $lastUpdatedFrom, $lastUpdatedTo);
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
        $lastUpdatedGroup->getDisplayGroup('lastUpdatedGroup')->setAttrib('class','search_fieldset_small')
                    ->setLegend('Last updated date');
    }
}


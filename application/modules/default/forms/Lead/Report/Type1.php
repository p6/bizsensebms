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


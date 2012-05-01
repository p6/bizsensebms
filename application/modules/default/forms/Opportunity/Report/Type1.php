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


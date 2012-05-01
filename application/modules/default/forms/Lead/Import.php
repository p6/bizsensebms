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

class Core_Form_Lead_Import extends Zend_Form
{
    /*
     * @return Zend_Form
     * Form to import leads
     */
    public function init()
    {

        $this->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');

        $leadSourceIdElement = new Core_Form_Lead_Element_LeadSource;
        $leadSourceId = $leadSourceIdElement->getElement();

        $leadStatusIdElement = new Core_Form_Lead_Element_LeadStatus;
        $leadStatusId = $leadStatusIdElement->getElement();


        $user = new Core_Model_User;
        $userData = $user->fetch(); 
        $userEmail = $userData->email;
        $userBranch = $userData->branch_name;
        
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Lead To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);

        $element = new Core_Form_Branch_Element_Branch;
        $branchId = $element->getElement();

        $csvFile = $this->createElement('file', 'lead_import_csv');
        $csvFile->setLabel('CSV file:')
             ->setRequired(true)    
            ->setDestination(APPLICATION_PATH . '/data/')
            ->setDescription('Upload CSV file')
            ->addValidator('Count', false, 1)    
            ->addValidator('Size', false, 102400) 
            ->addValidator('Extension', false, 'csv'); 

        $submit = $this->createElement('submit', 'submit', array (
                            'class' => 'submit_button'
                        )
                    );
        
	$hash = $this->createElement('hash', 'no_csrf_lead_import',
            array(
                'salt' => 'unique',
                'ignore' => true,
            )
        );
             

        $this->addElements(array($leadSourceId, $leadStatusId, 
            $assignedTo, $branchId, $csvFile, $submit, $hash));

    
        $metaDataGroup = $this->addDisplayGroup(array('lead_source_id', 'lead_status_id', 'assigned_to', 'branch_id', 
            'description'), 'metaData');
        $metaDataGroup->getDisplayGroup('metaData')->setLegend('Lead Meta Data');

        $this->addDisplayGroup(array('submit'), 'submit');


        /*
         * Add filters to all the elements
         * To trim the strings 
         * And to filter the HTML tags
         */
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->addFilter('StringTrim');
            $element->addFilter('StripTags');
        }
        return $this;

    }

}


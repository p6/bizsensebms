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
class Core_Form_Campaign_Create extends Zend_Form
{
    public function init() 
    {
        $name = $this->createElement('text', 'name')
                                ->setRequired(true)
                                ->addValidator(new Zend_Validate_StringLength(1,200))
                                ->setLabel('Name');

        $description = $this->createElement('textarea', 'description', array
                            (
                                'label' => 'Description',
                                'attribs' => array(
                                'rows' => 5,
                                'cols' => 30,
                                ),
                                'validators' => 
                                    array(
                                        'validator' =>  (new Zend_Validate_StringLength(0,250))
                                )
                            ) 
                        );

        $startDate = new Zend_Dojo_Form_Element_DateTextBox('start_date');
        $startDate->setLabel('Start Date');
        $startDate->setRequired(true);

        $endDate = new Zend_Dojo_Form_Element_DateTextBox('end_date');
        $endDate->setLabel('End Date');
        $endDate->setFormatLength('short')
                ->setRequired(true)
                ->addValidator(new BV_Validate_DateCompare)
                ->setInvalidMessage('Invalid date');

        $user = Zend_Registry::get('user');
        $userData = $user->fetch(); 
        $userId = $userData->user_id;
        $userBranch = $userData->branch_id;

        $assignToContainer = new Core_Form_User_Element_AssignedTo;
        $assignTo = $assignToContainer->getElement();
        $assignTo->setRequired(true);
        $assignTo->setLabel('Assign To');
        $assignTo->setValue($userId);

        $branchIdContainer = new Core_Form_Branch_Element_Branch;
        $branchId = $branchIdContainer->getElement();
        $branchId->setRequired(true);
        $branchId->setLabel('Branch');
        $branchId->setValue($userBranch);
        
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');

        $this->addElements(array($name, $description, $startDate, $endDate, 
               $assignTo, $branchId, $submit)); 
    }
}    

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
Class Core_Form_Finance_Payslip_Create extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    
    public function init()
    {
        if (Core_Model_User_Current::getId()) {
            $user = new Core_Model_User;

            $userData = $user->fetch(); 
            $userEmail = $userData->email;
        } else {
            $userEmail = '';
            $userBranch = '';
        }
        
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Employee E-mail');
        $assignedToContainer->setName('employee_id');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);     
        $this->addElement($assignedTo);
        
        
        $date = new Zend_Dojo_Form_Element_DateTextBox('date');
        $date->setRequired(true)
             ->setLabel('Date');
        $this->addElement($date);
        
        /*
         * EARNING FIELDS
         */
        $payrollFeildModel = new Core_Model_Finance_PayslipField;
        $earningFieldRecord =  $payrollFeildModel->getEnabledFieldsByType(
                              Core_Model_Finance_PayslipField::EARNING_FIELDS);
        $earningField = array();
        if ($earningFieldRecord) {
            for ($i = 0; $i < count($earningFieldRecord); $i++ ) {
                $field = $this->createElement('text', 
                                      $earningFieldRecord[$i]['machine_name'])
                         ->setLabel($earningFieldRecord[$i]['name']);
                $this->addElement($field);
                $earningField[$i] = $earningFieldRecord[$i]['machine_name'];
            }
            $earningFields = $this->addDisplayGroup($earningField,
                                                              'earningFields');
            $earningFields->getDisplayGroup('earningFields')->setLegend(
                                                             'Earning fields');
        }
        
        /*
         * DEDUCTION TAX FIELDS
         */  
        $deductionFieldRecord =  $payrollFeildModel->getEnabledFieldsByType(
                        Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS);

        $deductionField = array();
       
        if ($deductionFieldRecord) {
           for ($i = 0; $i < count($deductionFieldRecord); $i++ ) {
               $field = $this->createElement('text', 
                                    $deductionFieldRecord[$i]['machine_name'])
                         ->setLabel($deductionFieldRecord[$i]['name']);
               $this->addElement($field);
               $deductionField[] = $deductionFieldRecord[$i]['machine_name'];
             }
             $deductionFields = $this->addDisplayGroup($deductionField ,
                                                           'deductionFields');
             $deductionFields->getDisplayGroup('deductionFields')->setLegend(
                                                      'Deduction tax fields');
         }
         
        /*
         * DEDUCTION NON TAX FIELDS
         */  
        $nonDeductionFieldRecord =  $payrollFeildModel->getEnabledFieldsByType(
                    Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS);
        $deductionField = array();
        if ($nonDeductionFieldRecord) {
            for ($i = 0; $i < count($nonDeductionFieldRecord); $i++ ) {
                $field = $this->createElement('text',
                                 $nonDeductionFieldRecord[$i]['machine_name'])
                         ->setLabel($nonDeductionFieldRecord[$i]['name']);
                $this->addElement($field);
                $nonDeductionField[] = $nonDeductionFieldRecord[$i]['machine_name'];
            }
            $nonDeductionFields = $this->addDisplayGroup($nonDeductionField ,
                                                         'nonDeductionFields');
            $nonDeductionFields->getDisplayGroup('nonDeductionFields')->setLegend(
                                                  'Deduction non-tax fields');
        }
        

        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );
        
        
        
    }
}


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

Class Core_Form_Finance_Payment_SalaryChequeCreate extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $amount = $this->createElement('text', 'amount' )
                                ->setLabel('Amount')
                                ->setRequired(true)
                                ->addValidator(new Bare_Validate_IsNumeric);
        $this->addElements(array($amount));
        
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
                       
        $bankAccountId = new Core_Form_Finance_BankAccount_Element_BankAccount;
        $bankAccountElement = $bankAccountId->getElement();
        $bankAccountElement->setStoreParams(array('url'=>'/finance/bankaccount/jsonstore'));
        $bankAccountElement->setRequired(true);
        $this->addElement($bankAccountElement);
        
        $paymentDate = new Zend_Dojo_Form_Element_DateTextBox('date');
        $paymentDate->setRequired(true)
                    ->setLabel('Payment Date');
        $this->addElement($paymentDate);
        
        $this->addElement('text', 'instrument_number', array
            (
                'label' => 'Instrument Number',
                'required' => true,
            )
        );
        
        $instrumentDate = new Zend_Dojo_Form_Element_DateTextBox('instrument_date');
        $instrumentDate->setRequired(true)
                    ->setLabel('Instrument Date');
        $this->addElement($instrumentDate);
        
        $this->addElement('textarea', 'notes', array(
                'label' => 'Payment Notes',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );
        
        $element = new Core_Form_Branch_Element_Branch;
        $branchId = $element->getElement();
        $branchId->setRequired(true);
        $this->addElement($branchId);
        
        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );

    }
}


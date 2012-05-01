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
Class Core_Form_Finance_Receipt_CreateReceipt extends Zend_Form
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
        
        $this->addElement('text', 'instrument_account_no', array
            (
                'label' => 'Instrument Account Number',
                'required' => true,
            )
        );
        
        $this->addElement('text', 'bank_name', array
            (
                'label' => 'Bank',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 250))
                     )
            )
        );
        
        $this->addElement('text', 'bank_branch', array
            (
                'label' => 'Bank Branch',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 250))
                     )
            )
        );
        
        $this->addElement('text', 'instrument_number', array
            (
                'label' => 'Instrument Number',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 250))
                     )
            )
        );
        
        $receiptDate = new Zend_Dojo_Form_Element_DateTextBox('date');
        $receiptDate->setRequired(true)
                    ->setLabel('Receipt Date');
        $this->addElement($receiptDate);
        
        $receiptFrom = $this->createElement(
            'radio', 'from_type', array(
                'label'     =>  'Receipt To',
                'required'  => true,
            )
        );
        
        $receiptFrom->addValidator(new Core_Model_Opportunity_Validate_CustomerType());
        
        $receiptFrom->addMultiOptions(
            array(
                Core_Model_Finance_Receipt::FROM_TYPE_ACCOUNT => 'Account', 
                Core_Model_Finance_Receipt::FROM_TYPE_CONTACT =>'Contact'
            )
        );
        $this->addElement($receiptFrom);
        
        $accountId = new Core_Form_Account_Element_Account;
        $accountElement = $accountId->getElement();
        $accountElement->setStoreParams(array('url'=>'/account/jsonstore'));
        $this->addElement($accountElement); 

        $contactId = new Core_Form_Contact_Element_Contact;
        $contactElement = $contactId->getElement();
        $this->addElement($contactElement); 
        
        $user = new Core_Model_User;
        $userData = $user->fetch();
        $branchElement = new Core_Form_Branch_Element_Branch;
        $branchId = $branchElement->getElement();
        $branchId->setRequired(true);
        $branchId ->setAttrib('displayedValue', $userData->branch_name);
        $this->addElement($branchId);
        
        $bankaccountId = new Core_Form_Finance_BankAccount_Element_BankAccount;
        $bankaccountElement = $bankaccountId->getElement();
        $bankaccountElement->setStoreParams(array('url'=>'/finance/bankaccount/jsonstore'));
        $bankaccountElement->setRequired(true);
        $this->addElement($bankaccountElement);
        
        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );

    }
}


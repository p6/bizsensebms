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
Class Core_Form_Finance_BankAccount_Create extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $this->addElement('text', 'account_no', array
            (
                'label' => 'Account Number',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 50))
                     )
            )
        );
        
        $bank_name = $this->createElement('text', 'bank_name' )
                                ->setLabel('Bank Name')
                                ->setRequired(true) 
                                ->addValidator(new Zend_Validate_StringLength(0, 200))
                                ->addValidator(new Core_Model_Finance_Validate_DuplicateBankAccount());
        $this->addElements(array($bank_name));
        
        
        $this->addElement('text', 'bank_branch', array
            (
                'label' => 'Bank Branch',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 200))
                     )
            )
        );
        
        $this->addElement('text', 'opening_balance', array
            (
                'required' => true,
                'label' => 'Opening balance',
                'validators' => array (new Bare_Validate_IsNumeric),
            )
        );
    
        $this->addElement('radio', 'opening_balance_type', array
            (
                'label' => 'Balance type',
                'required' => true,
                'multiOptions' => array(
                    array('key'=>Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT, 'value'=>'Debit'),
                    array('key'=>Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_CREDIT, 'value'=>'Credit'),
                ),
            )
        );

        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );

    }
}


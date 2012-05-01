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
Class Core_Form_Finance_CashAccount_Create extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $name = $this->createElement('text', 'name' )
                                ->setLabel('Name')
                                ->setRequired(true)
                                ->addValidator(new Zend_Validate_StringLength(0, 200))
                                ->addValidator(new Zend_Validate_Db_NoRecordExists('cash_account', 'name'));
        $this->addElements(array($name));
        
        $user = new Core_Model_User;
        $userData = $user->fetch();
        $branchElement = new Core_Form_Branch_Element_Branch;
        $branchId = $branchElement->getElement();
        $branchId->setRequired(true);
        $branchId->setAttrib('displayedValue', $userData->branch_name);
        $this->addElement($branchId);

        $this->addElement('text', 'opening_balance', array
            (
                'required' => true,
                'label' => 'Opening balance',
                'value' => '0'
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
                'value' => '1'
            )
        );
        
        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'class' => 'submit_button',
                'ignore' => true
            )
        );

    }
}


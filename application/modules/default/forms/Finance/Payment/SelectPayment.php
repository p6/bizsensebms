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
Class Core_Form_Finance_Payment_SelectPayment extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $selectCashCheque = $this->createElement(
            'radio', 'mode', array(
                'label'     =>  'Mode of Payment',
                'required'  => true,
            )
        );
        
        $selectCashCheque->addMultiOptions(
            array(
                '1' => 'Cash', 
                '2' => 'Cheque'
            )
        );
        
        $this->addElement($selectCashCheque);
        
        $type = $this->createElement('select', 'type')
                ->setLabel('Type');

        $type->addMultiOption(
               Core_Model_Finance_Payment::PAYMENT_TO_SUNDRY_CREDITORS,
                                               'Payment To Sundry Creditors');
                                               
        $type->addMultiOption(
              Core_Model_Finance_Payment::PAYMENT_TOWARDS_EXPENSES,
                                               'Payment Towards Expenses');
                                               
        $type->addMultiOption(
            Core_Model_Finance_Payment::PAYMENT_TOWARDS_TDS, 'TDS');
        
        $type->addMultiOption(Core_Model_Finance_Payment::PAYMENT_TOWARDS_TAX,
                                                              'Tax Payments');
        
        $type->addMultiOption(
               Core_Model_Finance_Payment::PAYMENT_TOWARDS_SALARY, 
                                                           'Salary Payments');
        
        $type->addMultiOption(
               Core_Model_Finance_Payment::PAYMENT_TOWARDS_ADVANCE, 
                                                   'Employee advance payment');
                      
        $this->addElement($type);
        
        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );

    }
}


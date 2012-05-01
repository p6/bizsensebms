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


class Core_Form_Invoice_Create extends Zend_Form
{
    public function init()
    {
        $invoiceDate = new Zend_Dojo_Form_Element_DateTextBox('date');
        $invoiceDate->setRequired(true)
                    ->setLabel('Invoice Date');
        $this->addElement($invoiceDate);

        $invoiceTo = $this->createElement(
            'radio', 'to_type', array(
                'label'     =>  'Invoice to',
                'required'  => true,
            )
        );
        
        $invoiceTo->addValidator(new Core_Model_Opportunity_Validate_CustomerType());
        
        $invoiceTo->addMultiOptions(
            array(
                Core_Model_Invoice::TO_TYPE_ACCOUNT => 'Account', 
                Core_Model_Invoice::TO_TYPE_CONTACT =>'Contact'
            )
        );
        
        $this->addElement($invoiceTo);

        $accountId = new Core_Form_Account_Element_Account;
        $accountElement = $accountId->getElement();
        $accountElement->setStoreParams(array('url'=>'/account/jsonstore'));
        $this->addElement($accountElement); 

        $contactId = new Core_Form_Contact_Element_Contact;
        $contactElement = $contactId->getElement();
        $this->addElement($contactElement); 

        $campaignElementContainer = new Core_Form_Campaign_Element_Campaign;
        $campaignId = $campaignElementContainer->getElement();
        $this->addElement($campaignId);

        $branchElement = new Core_Form_Branch_Element_Branch;
        $branchId = $branchElement->getElement();
        $branchId->setRequired(true);
        $this->addElement($branchId);
        
        $this->addElement('text', 'discount_amount', array(
                'label' => 'Discount Amount',
                'value' => '0',
                'validators' => array(
                                     'validator' =>  (new Bare_Validate_IsNumeric)
                                    )
          
            )
        );
        
        $this->addElement('text', 'freight_amount', array(
                'label' => 'Freight Amount',
                'value' => '0',
                'validators' => array(
                                     'validator' =>  (new Bare_Validate_IsNumeric)
                                    )
                
            )
        );

        $this->addElement('text', 'purchase_order', array(
                'label' => 'Purchase order number',
            )
        );


        $this->addElement('textarea', 'notes', array(
                'label' => 'Invoice notes',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );

        $this->addElement('textarea', 'payment_terms', array(
                'label' => 'Payment terms',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );

        $this->addElement('textarea', 'delivery_terms', array(
                'label' => 'Delivery terms',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );

        $this->addElement('submit', 'submit', 
            array(
                'ignore' => true,
                'class' => 'submit_button'
            )
        );
    }
}


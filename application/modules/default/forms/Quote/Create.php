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

class Core_Form_Quote_Create extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'subject', array(
                'label' => 'Subject',
                'required'  => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 200))
                     )
            )
        );
        
        $quoteDate = new Zend_Dojo_Form_Element_DateTextBox('date');
        $quoteDate->setRequired(true)
                    ->setLabel('Quote Date');
        $this->addElement($quoteDate);

        $quoteTo = $this->createElement(
            'radio', 'to_type', array(
                'label'     =>  'Quote to',
                'required'  => true,
            )
        );
        
        $quoteTo->addValidator(new Core_Model_Opportunity_Validate_CustomerType());
        
        $quoteTo->addMultiOptions(
            array(
                Core_Model_QUOTE::TO_TYPE_ACCOUNT => 'Account', 
                Core_Model_QUOTE::TO_TYPE_CONTACT =>'Contact'
            )
        );
        
        $this->addElement($quoteTo);

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
        
        if (Core_Model_User_Current::getId()) {
            $user = new Core_Model_User;

            $userData = $user->fetch(); 
            $userEmail = $userData->email;
            $userBranch = $userData->branch_name;
        } else {
            $userEmail = '';
            $userBranch = '';
        }
        
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Lead To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $this->addElements(array($assignedTo));
        $assignedTo->setRequired(true);
            
        $status = new Core_Form_Quote_Element_QuoteStatus;
        $statusElement = $status->getElement();
        $statusElement->setStoreParams(array('url'=>'/quotestatus/jsonstore'));
        $statusElement->setRequired(true);
        $this->addElement($statusElement);
        
        $this->addElement('text', 'discount_amount', array(
                'label' => 'Discount Amount',
                'value' => '0'
            )
        );

        $this->addElement('textarea', 'description', array(
                'label' => 'Quote description',
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
        
        $this->addElement('textarea', 'internal_notes', array(
                'label' => 'Internal notes',
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


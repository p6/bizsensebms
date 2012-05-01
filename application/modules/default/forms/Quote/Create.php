<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation,  version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category BizSense
 * @package  Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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


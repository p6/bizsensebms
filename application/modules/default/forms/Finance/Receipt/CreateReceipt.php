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
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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


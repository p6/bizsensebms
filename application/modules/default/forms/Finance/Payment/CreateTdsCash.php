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
Class Core_Form_Finance_Payment_CreateTdsCash extends Zend_Form
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
        
        $taxAmount = $this->createElement('text', 'tax_amount' )
                                ->setLabel('Tax Amount')
                                ->setRequired(true)
                                ->addValidator(new Core_Model_Finance_Validate_TdsAmount());
                                #->addValidator(new Zend_Validate_Float());
        $this->addElements(array($taxAmount));
        
                
        $tdsLedger = new Core_Form_Finance_Ledger_Element_TdsLedger;
        $tdsLedgerElement = $tdsLedger->getElement();
        $tdsLedgerElement->setStoreParams(array('url'=>'/finance/ledger/tdsstore'));
        $this->addElement($tdsLedgerElement); 
          
        $indirectExpenseLedger = new Core_Form_Finance_Ledger_Element_ExpenseLedger;
        $indirectExpenseElement = $indirectExpenseLedger->getElement();
        $indirectExpenseElement->setStoreParams(array('url'=>'/finance/ledger/jsonstore'));
        $this->addElement($indirectExpenseElement);
        
        $cashaccountId = new Core_Form_Finance_CashAccount_Element_CashAccount;
        $cashaccountElement = $cashaccountId->getElement();
        $cashaccountElement->setStoreParams(array('url'=>'/finance/cashaccount/jsonstore'));
        $this->addElement($cashaccountElement); 
        
        $paymentDate = new Zend_Dojo_Form_Element_DateTextBox('date');
        $paymentDate->setRequired(true)
                    ->setLabel('Payment Date');
        $this->addElement($paymentDate);
        
        $this->addElement('textarea', 'notes', array(
                'label' => 'Payment notes',
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


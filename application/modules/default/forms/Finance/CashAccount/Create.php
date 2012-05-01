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


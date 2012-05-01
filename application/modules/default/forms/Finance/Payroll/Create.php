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
Class Core_Form_Finance_Payroll_Create extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    
    public function init()
    {
        if (Core_Model_User_Current::getId()) {
            $user = new Core_Model_User;

            $userData = $user->fetch(); 
            $userEmail = $userData->email;
        } else {
            $userEmail = '';
            $userBranch = '';
        }
        
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Employee E-mail');
        $assignedToContainer->setName('employee_id');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);      
        
        $date = new Zend_Dojo_Form_Element_DateTextBox('date');
        $date->setRequired(true)
             ->setLabel('Date');
        $this->addElement($date);
        
        $payslipFieldModel = new Core_Model_Finance_PayslipField;
        $enabledFildsList = $payslipFieldModel->getEnabledFields();
        
        foreach ($enabledFildsList as $key => $value) {
           $key = $this->createElement('text', $key) 
                       ->addValidator(new Bare_Validate_IsNumeric)
                       ->setLabel($key);
             $this->addElement($key);
        }
     
        $indirectExpenseLedger = new Core_Form_Finance_Ledger_Element_ExpenseLedger;
        $indirectExpenseElement = $indirectExpenseLedger->getElement();
        $indirectExpenseElement->setStoreParams(array('url'=>'/finance/ledger/jsonstore'));
        $indirectExpenseElement->setRequired(true);
        $this->addElement($indirectExpenseElement);

        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true
            )
        );
        
        
        
    }
}


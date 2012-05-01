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
Class Core_Form_Finance_Payslip_Create extends Zend_Form
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
        $this->addElement($assignedTo);
        
        
        $date = new Zend_Dojo_Form_Element_DateTextBox('date');
        $date->setRequired(true)
             ->setLabel('Date');
        $this->addElement($date);
        
        /*
         * EARNING FIELDS
         */
        $payrollFeildModel = new Core_Model_Finance_PayslipField;
        $earningFieldRecord =  $payrollFeildModel->getEnabledFieldsByType(
                              Core_Model_Finance_PayslipField::EARNING_FIELDS);
        $earningField = array();
        if ($earningFieldRecord) {
            for ($i = 0; $i < count($earningFieldRecord); $i++ ) {
                $field = $this->createElement('text', 
                                      $earningFieldRecord[$i]['machine_name'])
                         ->setLabel($earningFieldRecord[$i]['name']);
                $this->addElement($field);
                $earningField[$i] = $earningFieldRecord[$i]['machine_name'];
            }
            $earningFields = $this->addDisplayGroup($earningField,
                                                              'earningFields');
            $earningFields->getDisplayGroup('earningFields')->setLegend(
                                                             'Earning fields');
        }
        
        /*
         * DEDUCTION TAX FIELDS
         */  
        $deductionFieldRecord =  $payrollFeildModel->getEnabledFieldsByType(
                        Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS);

        $deductionField = array();
       
        if ($deductionFieldRecord) {
           for ($i = 0; $i < count($deductionFieldRecord); $i++ ) {
               $field = $this->createElement('text', 
                                    $deductionFieldRecord[$i]['machine_name'])
                         ->setLabel($deductionFieldRecord[$i]['name']);
               $this->addElement($field);
               $deductionField[] = $deductionFieldRecord[$i]['machine_name'];
             }
             $deductionFields = $this->addDisplayGroup($deductionField ,
                                                           'deductionFields');
             $deductionFields->getDisplayGroup('deductionFields')->setLegend(
                                                      'Deduction tax fields');
         }
         
        /*
         * DEDUCTION NON TAX FIELDS
         */  
        $nonDeductionFieldRecord =  $payrollFeildModel->getEnabledFieldsByType(
                    Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS);
        $deductionField = array();
        if ($nonDeductionFieldRecord) {
            for ($i = 0; $i < count($nonDeductionFieldRecord); $i++ ) {
                $field = $this->createElement('text',
                                 $nonDeductionFieldRecord[$i]['machine_name'])
                         ->setLabel($nonDeductionFieldRecord[$i]['name']);
                $this->addElement($field);
                $nonDeductionField[] = $nonDeductionFieldRecord[$i]['machine_name'];
            }
            $nonDeductionFields = $this->addDisplayGroup($nonDeductionField ,
                                                         'nonDeductionFields');
            $nonDeductionFields->getDisplayGroup('nonDeductionFields')->setLegend(
                                                  'Deduction non-tax fields');
        }
        

        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );
        
        
        
    }
}


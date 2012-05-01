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
Class Core_Form_Finance_Payroll_Settings extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $basic_salary = $this->createElement('checkbox', 'basic_salary')
                            ->setLabel('Basic salary');
        
        
        $special_allowance = $this->createElement('checkbox', 'special_allowance')
                            ->setLabel('Special allowance');
                            
        $HRA = $this->createElement('checkbox', 'HRA')
                            ->setLabel('HRA');
                            
        $personal_pay = $this->createElement('checkbox', 'personal_pay')
                            ->setLabel('Personal pay');   
                                   
        $dearness_allowance = $this->createElement('checkbox', 'dearness_allowance')
                            ->setLabel('Dearness allowance');    
        
        $medical_allowance = $this->createElement('checkbox', 'medical_allowance')
                            ->setLabel('Medical allowance');             
                            
        $shift_allowance = $this->createElement('checkbox', 'shift_allowance')
                            ->setLabel('Shift allowance');  
        
        $CCA = $this->createElement('checkbox', 'CCA')
                            ->setLabel('CCA');  
                            
        $transport_allowance = $this->createElement('checkbox', 'transport_allowance')
                            ->setLabel('Transport allowance');                     
                            
        $leave_travel_allowance = $this->createElement('checkbox', 'leave_travel_allowance')
                            ->setLabel('Leave travel allowance');                     
                            
        $performance_allowance = $this->createElement('checkbox', 'performance_allowance')
                            ->setLabel('Performance allowance');  
                            
        $canteen_subsidy = $this->createElement('checkbox', 'canteen_subsidy')
                            ->setLabel('Canteen subsidy');     
                            
        $special_living_allowance = $this->createElement('checkbox', 'special_living_allowance')
                            ->setLabel('Special living allowance');    
                            
        $other_allowances = $this->createElement('checkbox', 'other_allowances')
                            ->setLabel('Other allowances');                      
                            
        $salary_arrears = $this->createElement('checkbox', 'salary_arrears')
                            ->setLabel('Salary arrears');   
        
        $this->addElements(array($basic_salary, $special_allowance, $HRA,
               $personal_pay, $dearness_allowance, $medical_allowance, 
               $medical_allowance, $shift_allowance, $CCA, $transport_allowance, 
               $leave_travel_allowance, $performance_allowance, $canteen_subsidy
               , $special_living_allowance, $other_allowances, 
               $salary_arrears));
           
        $provident_fund = $this->createElement('checkbox', 'provident_fund')
                            ->setLabel('Provident fund');   
        
        $voluntary_PF = $this->createElement('checkbox', 'voluntary_PF')
                            ->setLabel('Voluntary PF');  
        
        $professional_Tax = $this->createElement('checkbox', 'professional_Tax')
                            ->setLabel('Professional Tax');
        
        $ESI = $this->createElement('checkbox', 'ESI')
                            ->setLabel('ESI');
        
        $income_tax = $this->createElement('checkbox', 'income_tax')
                            ->setLabel('Income tax');
        
        $this->addElements(array($provident_fund, $voluntary_PF, 
               $professional_Tax, $ESI, $income_tax));
               
        $rent_recovery = $this->createElement('checkbox', 'rent_recovery')
                            ->setLabel('Rent recovery'); 
        
        $loan_deduction = $this->createElement('checkbox', 'loan_deduction')
                            ->setLabel('Loan deduction'); 
        
        $insurance_deduction = $this->createElement('checkbox', 'insurance_deduction')
                            ->setLabel('Insurance deduction');
        
        $club_deductions = $this->createElement('checkbox', 'club_deductions')
                            ->setLabel('Club deductions');
                            
        $provident_fund_arrears = $this->createElement('checkbox', 'provident_fund_arrears')
                            ->setLabel('Provident fund arrears');
                            
        $advance = $this->createElement('checkbox', 'advance')
                            ->setLabel('Advance');
         
         $this->addElements(array($rent_recovery, $loan_deduction, 
               $insurance_deduction, $club_deductions, $provident_fund_arrears,
               $advance));
         
        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true
            )
        );
        
        $earningFields = $this->addDisplayGroup(array (
            'basic_salary', 
            'special_allowance', 
            'HRA',
            'personal_pay', 
            'dearness_allowance',
            'medical_allowance',
            'shift_allowance',
            'CCA',
            'transport_allowance',
            'leave_travel_allowance',
            'performance_allowance',
            'canteen_subsidy',
            'special_living_allowance',
            'other_allowances',
            'salary_arrears',
            ), 
           'earningFields');
           
        $deductionFields = $this->addDisplayGroup(array (
            'provident_fund', 
            'voluntary_PF', 
            'professional_Tax', 
            'ESI',
            'income_tax'
            ), 
           'deductionFields');
           
        $nonDeductionFields = $this->addDisplayGroup(array (
            'rent_recovery', 
            'loan_deduction', 
            'insurance_deduction', 
            'club_deductions',
            'provident_fund_arrears',
            'advance',
            'submit'
            ), 
           'nonDeductionFields');
        
        
        $earningFields->getDisplayGroup('earningFields')->setLegend('Earning fields');
        
        $deductionFields->getDisplayGroup('deductionFields')->setLegend('Deduction tax fields');
        
        $nonDeductionFields->getDisplayGroup('nonDeductionFields')->setLegend('Deduction non-tax fields');
        
        $payrollFeildModel = new Core_Model_Finance_PayslipField;
        $enabledFeilds =  $payrollFeildModel->getEnabledFields();
    }
}


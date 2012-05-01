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


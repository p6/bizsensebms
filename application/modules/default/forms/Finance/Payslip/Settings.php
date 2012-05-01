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
Class Core_Form_Finance_Payslip_Settings extends Zend_Form
{
    /**
     * @return Zend_Form
     */
    public function init()
    {
        /*
         * EARNING FIELDS
         */
        $payrollFeildModel = new Core_Model_Finance_PayslipField;
        $earningFieldRecord =  $payrollFeildModel->getFieldsByType(
                              Core_Model_Finance_PayslipField::EARNING_FIELDS);
        
        $earningField = array();
        for ($i = 0; $i < count($earningFieldRecord); $i++ ) {
           $field = $this->createElement('checkbox', 
                                      $earningFieldRecord[$i]['machine_name'])
                         ->setLabel($earningFieldRecord[$i]['name']);
           $this->addElement($field);
           $earningField[$i] = $earningFieldRecord[$i]['machine_name'];
        }
         
        $earningFields = $this->addDisplayGroup($earningField,'earningFields');
        $earningFields->getDisplayGroup('earningFields')->setLegend(
                                                             'Earning fields');
        
        /*
         * DEDUCTION TAX FIELDS
         */  
        $deductionFieldRecord =  $payrollFeildModel->getFieldsByType(
                        Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS);

        $deductionField = array();
        for ($i = 0; $i < count($deductionFieldRecord); $i++ ) {
           $field = $this->createElement('checkbox', 
                                    $deductionFieldRecord[$i]['machine_name'])
                         ->setLabel($deductionFieldRecord[$i]['name']);
           $this->addElement($field);
           
           $ledger = $deductionFieldRecord[$i]['machine_name'].'_ledger';
            
           $field->addValidator(new 
           Core_Model_Finance_Payslip_Validate_Ledger($ledger));
           
          
           $ledgerLabel = $deductionFieldRecord[$i]['name'].' Ledger';
           $tdsLedger = new Core_Form_Finance_Ledger_Element_Ledgers;
           $tdsLedger->setName($ledger);
           $tdsLedger->setLabel($ledgerLabel);
           $tdsLedgerElement = $tdsLedger->getElement();
           $tdsLedgerElement->setStoreParams(array('url'=>'/finance/ledger/ledgerstore'));
           $this->addElement($tdsLedgerElement);
           $deductionField[] = $deductionFieldRecord[$i]['machine_name'];
           $deductionField[] = $ledger;
        }
        $deductionFields = $this->addDisplayGroup($deductionField ,
                                                           'deductionFields');
        $deductionFields->getDisplayGroup('deductionFields')->setLegend(
                                                      'Deduction tax fields');
         
        /*
         * DEDUCTION NON TAX FIELDS
         */  
        $nonDeductionFieldRecord =  $payrollFeildModel->getFieldsByType(
                    Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS);
        
        $deductionField = array();
        for ($i = 0; $i < count($nonDeductionFieldRecord); $i++ ) {
           $field = $this->createElement('checkbox',
                                 $nonDeductionFieldRecord[$i]['machine_name'])
                         ->setLabel($nonDeductionFieldRecord[$i]['name']);
           $this->addElement($field);
           
           $ledger = $nonDeductionFieldRecord[$i]['machine_name'].'_ledger';
            
           $field->addValidator(new 
           Core_Model_Finance_Payslip_Validate_Ledger($ledger));
           
          
           $ledgerLabel = $nonDeductionFieldRecord[$i]['name'].' Ledger';
           $tdsLedger = new Core_Form_Finance_Ledger_Element_Ledgers;
           $tdsLedger->setName($ledger);
           $tdsLedger->setLabel($ledgerLabel);
           $tdsLedgerElement = $tdsLedger->getElement();
           $tdsLedgerElement->setStoreParams(array('url'=>'/finance/ledger/ledgerstore'));
           $this->addElement($tdsLedgerElement);
           $nonDeductionField[] = $nonDeductionFieldRecord[$i]['machine_name'];
           $nonDeductionField[] = $ledger;
        }
        
        $nonDeductionFields = $this->addDisplayGroup($nonDeductionField ,
                                                        'nonDeductionFields');
        $nonDeductionFields->getDisplayGroup('nonDeductionFields')->setLegend(
                                                  'Deduction non-tax fields');
        
        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );
            
    }
}

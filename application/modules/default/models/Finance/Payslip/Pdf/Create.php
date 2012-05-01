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

class Core_Model_Finance_Payslip_Pdf_Create extends fpdf
{

    /**
     * @var int payslip Id
     */
    protected $_payslipId; 

    /**
     * @var array payslipRecord summary data to display
     */
    protected $_payslipRecord = array();
   
    /**
     * @var array  summary data to display
     */
    protected $_payslipItems = array();
    
    /**
     * @var array organization meta data
     */
    protected $_orgData = array();

    /**
     * Initialize the data
     */
    public function init()
    {
          $this->_orgData['organization'] = new Core_Model_Org;       
    }

    /**
     *@var array data to display
     */
    public function setSummaryDetails($payslipId,$payslipRecord, $payslipItems)
    {
        $this->_payslipId = $payslipId;
        $this->_payslipRecord = $payslipRecord;
        $this->_payslipItems = $payslipItems;
    }

    /**
     * Process the PDF document generation process
     */
    public function run()
    {
        $this->init();
        $this->SetFont('Arial','',10);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetTitle('Pay slip summary');
        $this->displaySummary();
        $this->signature();
    }

    /**
     * Print Logo if exists, Organization Name,Adrress,
     * and Title of the Document
     */
    function Header()
    {
        $organization = $this->_orgData['organization'];
        $websiteUrl = $organization->getWebsiteUrl();

        if (is_readable(APPLICATION_PATH . '/data/documents/image/logo.jpg')) {
            $this->Image(APPLICATION_PATH . '/data/documents/image/logo.jpg',
                10,8,0,0,'jpg',$websiteUrl);
        }
        $this->SetFont('Arial','B',10);
        $this->Cell(42);
        
        $this->Cell(110,7,$organization->getName(),0,0,'C',false,'');

        $this->Ln(5);
            
        $this->SetTextColor(255,0,0);
        $this->SetFont('Arial','B',18);
        $this->cell(155);
        $this->Cell(30,7,'Report',0);
        $this->SetFillColor(0,0,0);
        $this->Line(5,30,200,30);
        $this->Ln(20);
    }
    
    /**
     * Display Page number at end of each page
     */
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,5,'Page '.$this->PageNo().'/{nb}',0,2,'C');
    }

    /**
     * display summary data 
     */
    public function displaySummary()
    {
        $payslipRecord = $this->_payslipRecord;
        $payslipItems = $this->_payslipItems;

        $this->SetFillColor(255,255,255);

        $this->Cell(0, 5,"Pay slip summary", 'B', '1', 'C', true);

        $this->SetTextColor(1,2,5);
     
        $this->Cell(28, 5,"Employee Name :", 0, '0', 'L');
        $userModel = new Core_Model_User($payslipRecord['employee_id']);
        $employeeName = $userModel->getProfile()->getFullName();
        $this->Cell(90, 5,"$employeeName", 0, '1', 'L');
        $this->Cell(28, 5,"Date:", 0, '0', 'L');
        
        $timestampHelper = new BV_View_Helper_TimestampToDocument;
        $date = $timestampHelper->timestampToDocument($payslipRecord['date']);
        $this->Cell(90, 5,"$date", 0, '1', 'L');

        $this->SetTextColor(255,255,255);
        $this->SetFillColor(100,150,250);
      
        $this->ln(6); 
        $this->Cell(40, 5,"Earnings", 1, '0', 'C', true);
        $this->Cell(40, 5,"Amount", 1, '1', 'C', true);
        $this->SetTextColor(0,0,0);
       
        
        $earningFields = array();
        $deductionTaxFields = array();
        $deductionNonTaxFields = array();
        $payslipFieldModel = new Core_Model_Finance_PayslipField;
        
        foreach ($payslipItems as $name => $amount) {
        
           $type = $payslipFieldModel->getTypeByName($name);
           
           if ($type == Core_Model_Finance_PayslipField::EARNING_FIELDS) {
               $earningFields[$name] = $amount;
           }
           
           if ($type == Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS) {
               $deductionTaxFields[$name] = $amount;
           }
           
           if ($type == Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS) {
               $deductionNonTaxFields[$name] = $amount;
           }
          
        }
        
        if ($earningFields) {
            foreach ($earningFields as $name => $amount) {
                $this->Cell(40, 5, $name, 1, '0', 'L', false);
                $this->Cell(40, 5, $amount, 1, '1', 'L', false);
            }
        }


        $this->Ln(10);

        if ($deductionTaxFields) {
            $this->SetTextColor(255,255,255);
            $this->Cell(40, 5,"Deduction Tax Fields", 1, '0', 'C', true);
            $this->Cell(40, 5,"Amount", 1, '1', 'C', true);
            $this->SetTextColor(0,0,0);
            foreach ($deductionTaxFields as $name => $amount) {
                $this->Cell(40, 5, $name, 1, '0', 'L', false);
                $this->Cell(40, 5, $amount, 1, '1', 'L', false);
           }
        }

        $this->Ln(10);
        if ($deductionNonTaxFields) {
            $this->SetTextColor(255,255,255);
            $this->Cell(40, 5,"Deduction Non Tax Fields", 1, '0', 'C', true);
            $this->Cell(40, 5,"Amount", 1, '1', 'C', true);
            $this->SetTextColor(0,0,0);
            foreach ($deductionNonTaxFields as $name => $amount) {
                $this->Cell(40, 5, $name, 1, '0', 'L', false);
                $this->Cell(40, 5, $amount, 1, '1', 'L', false);
            }
        }

        $this->Ln(10);
        $this->Cell(40, 5,"Total", 1, '0', 'C');
        $payslipModel = new Core_Model_Finance_Payslip($this->_payslipId);

        $netPayableSalary =  $payslipModel->getPayableSalaryAmount();
        $this->Cell(40, 5,"$netPayableSalary", 1, '1', 'C');

        $this->Ln(5);
        $this->Cell(30, 5,"Amount In Words :", 0, '0', 'L');
        $wordsHelper = new BV_View_Helper_NumberToWord; 
        $amountInWords = $wordsHelper->numberToWord($netPayableSalary);
        $this->Cell(0, 5,"$amountInWords", 0, '0', 'L');
    }

    /**  
     * Display Signature  
     */
    public function signature()
    {
        $this->Ln(10);
        $this->Cell(90, 5, 'For', 0, '1', 'L');
        $this->Ln(10);
        $organization = new Core_Model_Org;        
        $this->Cell(90,5,$organization->getName(),0,0,'L',false,'');
        $this->Ln(10);
        $this->Cell(0,5,'This is a computer generated document.',0,0,'L');
    }
}

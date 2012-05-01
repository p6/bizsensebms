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

class Core_Model_Quote_Pdf_Base extends FPDF
{
    public $db;
    private $_branch;    
    public function init()
    {
         $this->db = Zend_Registry::get('db');

    } 

    /*
     * Sets the branch object
     */
    public function setBranch($branch)
    {
        $this->_branch = $branch;     
    }
    
    /*
     * Page header
     */
    function Header()
    {

        $logo = Core_Model_SiteLogo::getLogoForDocumentsFileName();
        $this->Image($logo,10,8,33);
        $this->SetFont('Arial','B',15);

        /*
         * Move to the right
         */
        $this->Cell(80);

        $org = new Core_Model_Org;
        $org = $org->fetch();
        $companyName = $org->company_name;

    
        /* 
         * Title
         */ 
        $this->Cell(30, 5, $companyName, 0, 0, 'C');

        /*
         * Line break
         */
        $this->Ln(5);

        $this->SetFont('Arial','',8);
        $this->Cell(200, 10, $this->_branch->address_line_1 . ',', 0, 0, 'C');
        $this->Ln(3);
        if (strlen($this->_branch->address_line_2)>1) {
            $this->Cell(200, 10, $this->_branch->address_line_2 . ',', 0, 0, 'C');
            $this->Ln(3);
        }
        if (strlen($this->_branch->address_line_3)>1) {
            $this->Cell(200, 10, $this->_branch->address_line_3 . ',', 0, 0, 'C');
            $this->Ln(3);
        }
        if (strlen($this->_branch->address_line_4)>1) {
            $this->Cell(200, 10, $this->_branch->address_line_4 . ',', 0, 0, 'C');
            $this->Ln(5);
        }
        $this->Cell(200, 10, $this->_branch->city . ', ' . $this->_branch->state . ' - ' .
                $this->_branch->postal_code . ', ' . $this->_branch->country, 0, 0, 'C');
        $this->Ln(10);
    }

    /*
     * Page footer
     */
    function Footer()
    {
        /*
         * Position at 1.5 cm from bottom
         */
        $this->SetY(-15);

        /*
         * Arial italic 8
         */
        $this->SetFont('Arial','I',8);

        /*
         * Page number
         */
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

       
    /*
     * Colored table
     */
    function FancyTable($header,$data)
    {
        $this->SetFont('Arial','I',8);

        //Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');

        //Header
        $w=array(10,20,20,20,20,20,20,20);
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        }
        $this->Ln();

        //Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');

        //Data
        $fill=false;
        $serialNumber = 0;
        $totalAmountBeforeTax = 0;
        $totalTaxAmount = 0;
        $grandTotal = 0;
        foreach($data as $row)
        {
            $serialNumber++;

            $unitPrice = $row['unit_price'];
            $taxPercentage = $row['percentage'];
            $quantity = $row['quantity'];
            $amount = ($quantity * $unitPrice);

            $this->Cell($w[0],6,$serialNumber,'LR',0,'L',$fill);
            $this->Cell($w[1],6,$row['product_name'],'LR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($unitPrice),'LR',0,'R',$fill);
            $this->Cell($w[3],6,$quantity,'LR',0,'R',$fill);
        
            $this->Cell($w[4],6,number_format($amount),'LR',0,'R',$fill);
            $totalAmountBeforeTax += $amount;

            $this->Cell($w[4],6,$row['tax_name'],'LR',0,'R',$fill);
            
            $taxAmount = ($amount * ($taxPercentage/100));
            $totalTaxAmount += $taxAmount;
            
            $subTotal = ($taxAmount + $amount);
            $grandTotal += $subTotal;
            
            $this->Cell($w[4],6,number_format($taxAmount),'LR',0,'R',$fill);
            $this->Cell($w[4],6,number_format($subTotal),'LR',0,'R',$fill);
           
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w),0,'','T');

        /*
         * Print totals
         */
        $this->Cell(50,5,"", 0, 1, 'L');
        $this->Cell(50,5,"Total amount before tax", 1, 0, 'L');
        $this->Cell(50,5,number_format($totalAmountBeforeTax),1,1, 'L');
        $this->Cell(50,5,"Total tax amount", 1, 0, 'L');
        $this->Cell(50,5,number_format($totalTaxAmount), 1,1, 'L');
        $this->Cell(50,5,"Grand total", 1, 0, 'L');
        $this->Cell(50,5,number_format($grandTotal), 1,1, 'L');

        $this->ln(3);


    }
    
    //Simple table
    function BasicTable($header,$data)
    {
        //Header
        foreach($header as $col) {
            $this->Cell(40,7,$col,1);
        }
        
        $this->Ln();
        //Data
        foreach($data as $row)
        {
            foreach($row as $col){
                $this->Cell(40,6,$col,1);
            }
            $this->Ln();
        }
    }


 
}



<?php
/*
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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

require('fpdf/fpdf.php');

/** 
 * Quote PDF Base class
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



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

class Core_Model_Finance_Ledger_Pdf_Outstanding extends fpdf
{
    /**
     * @var array sundryDebtors summary data to display
     */
    protected $_sundryDebtors = array();
   
    /**
     * @var array sundryCreditors summary data to display
     */
    protected $_sundryCreditors = array();
    
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
    public function setSummaryDetails($sundryDebtors, $sundryCreditors)
    {
        $this->_sundryDebtors = $sundryDebtors;
        $this->_sundryCreditors = $sundryCreditors;
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
        $this->SetTitle('Outstandings summary');
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
        $this->cell(90,10,"Outstandings Summary", 0, 1, 'C',false);

        $sundryDebtors = $this->_sundryDebtors;
        $sundryCreditors = $this->_sundryCreditors;

        $this->SetFillColor(255,255,255);

        $this->Cell(90, 5,"From Sundry Debtors", 0, '1', 'C', true);
        $this->SetTextColor(255,255,255);
        $this->SetFillColor(100,150,250);
        
        $this->Cell(40, 5,"Ledger Name", 1, '0', 'L', true);
        $this->Cell(40, 5,"Balance", 1, '1', 'L', true);
        $this->SetTextColor(0,0,0);
       
 
        foreach($sundryDebtors as $keys => $values) {
            $this->Cell(40, 5, $keys, 1, '0', 'L', false);
            $balance = abs($values);    
            $this->Cell(40, 5, $balance, 1, '0', 'L', false);                    
        }

        $this->Ln(10);
        $this->SetFillColor(255,255,255);
        $this->Cell(90, 10,"From Sundry Creditors", 0, '1', 'C', true);
        $this->SetTextColor(255,255,255);
        $this->SetFillColor(100,150,250);

        $this->Cell(40, 5,"Ledger Name", 1, '0', 'L', true);
        $this->Cell(40, 5,"Balance", 1, '1', 'L', true);
        $this->SetTextColor(0,0,0);        

        foreach($sundryCreditors as $keys => $values) {
            $this->Cell(40, 5, $keys, 1, '0', 'L', false);
            $balance = abs($values);
            $this->Cell(40, 5, $balance, 1, '0', 'L', false);
        }

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

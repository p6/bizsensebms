<?php
require(APPLICATION_PATH . '/../library/fpdf/fpdf.php');
class Core_Model_Finance_Ledger_Pdf_Create extends fpdf
{
    /**
     * @var array ledger summary data to display
     */
    protected $_ledgerSummary = array();

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
    public function setSummaryDetails($data)
    {
        $this->_ledgerSummary = $data;
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
        $this->SetTitle('Quote');
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
        $this->cell(90,10,"Ledger Wise Summary", 0, 1, 'C',false);
        $summary = $this->_ledgerSummary;    
        $this->SetTextColor(255,255,255);
        $this->SetFillColor(100,150,250);

        $this->Cell(50, 5,"Ledger Name", 1, '0', 'L', true);
        $this->Cell(50, 5,"Balance", 1, '1', 'L', true);
        $this->SetTextColor(0,0,0);

        foreach($summary as $key => $value) {
            $this->Cell(50, 5, $value['ledger_name'], 1, '0', 'L', false);
            $totalBalance =  $value['balance']; 
            if ($totalBalance > 0) {
                $total =  $value['balance'] . " Cr" ;
                $this->Cell(50, 5,$total, 1, '1', 'L', false);
            }else {
                $total =  abs($value['balance']) . " Dr" ;
                $this->Cell(50, 5,$total, 1, '1', 'L', false);
            }
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

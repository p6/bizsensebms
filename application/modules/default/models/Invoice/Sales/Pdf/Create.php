<?php
require(APPLICATION_PATH . '/../library/fpdf/fpdf.php');
class Core_Model_Invoice_Sales_Pdf_Create extends fpdf
{
    /**
     * @var array ledger summary data to display
     */
    protected $_salesRegister = array();

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
        $this->_salesRegister = $data;
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
        $this->SetTitle('Sales Register Sumary');
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
        $this->cell(90,10," Sales Registry Summary", 0, 1, 'C',false);
        $summary = $this->_salesRegister;    
        $this->SetTextColor(255,255,255);
        $this->SetFillColor(100,150,250);

        $this->Cell(40, 5,"Date", 1, '0', 'L', true);
        $this->Cell(40, 5,"Particulars", 1, '0', 'L', true);
        $this->Cell(40, 5,"Type", 1, '0', 'L', true);
        $this->Cell(40, 5,"Id", 1, '0', 'L', true);
        $this->Cell(40, 5,"Debit Amount", 1, '1', 'L', true);
      //  $this->Cell(30, 5,"Credit Amount", 1, '1', 'L', true);
        $this->SetTextColor(0,0,0);
        
        foreach($summary as $keys => $values) {
            foreach($values as $key => $value) {
                if(!is_array($value)){
                    $this->Cell(40, 5, $value, 1, '0', 'L', false);
                }
            }
            $this->Ln(5);
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

<?php
require(APPLICATION_PATH . '/../library/fpdf/fpdf.php');
class Core_Model_Finance_Ledger_Pdf_TrialBalance extends fpdf
{
    /**
     * @var array trail balance summary data to display
     */
    protected $_trialBalance = array();

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
        $this->_trialBalance = $data;
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
        $this->SetTitle('Trial Balance');
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
     * display trail balance summary data 
     */
    public function displaySummary()
    {
        $this->cell(90,10,"Trial Balance Summary", 0, 1, 'C',false);
        $summary = $this->_trialBalance;    
        $this->SetTextColor(255,255,255);
        $this->SetFillColor(100,150,250);

        $this->Cell(35, 5,"Ledger Name", 1, '0', 'C', true);
        $this->Cell(30, 5,"Debit", 1, '0', 'C', true);
        $this->Cell(30, 5,"Credit", 1, '1', 'C', true);
        $this->SetTextColor(0,0,0);

        $totalDebit = 0;
        $totalCredit = 0;

        for($i = 0; $i <= sizeof($summary)-1; $i += 1) {
            $ledgerName = $summary[$i]['ledger_name'];
            $this->Cell(35, 5,"$ledgerName", 1, '0', 'L');

            switch ($summary[$i]['balance_type']) {
                case Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_DEBIT :
                    $balance = $summary[$i]['balance'];
                    $this->Cell(30, 5,"$balance", 1, '0', 'C');
                    $this->Cell(30, 5,"", 1, '1', 'C');
                    $totalDebit = $totalDebit + $summary[$i]['balance'];
                break;
                
                case Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_CREDIT :
                    $this->Cell(30, 5,"", 1, '0', 'C');
                    $balance = abs($summary[$i]['balance']);
                    $this->Cell(30, 5,"$balance", 1, '1', 'C');
                    $totalCredit = $totalCredit + $summary[$i]['balance'];
                break;
            
                case Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_DEBITCREDIT :
                    if ($summary[$i]['balance'] < 0) {
                        $this->Cell(30, 5,"", 1, '0', 'C');
                        $balance = abs($summary[$i]['balance']);
                        $this->Cell(30, 5,"$balance", 1, '1', 'C');
                        $totalDebit = $totalDebit + $summary[$i]['balance'];
                    }else {
                        $this->Cell(30, 5,"", 1, '0', 'C');
                        $balance = abs($summary[$i]['balance']);
                        $this->Cell(30, 5,"$balance", 1, '1', 'C');
                        $totalCredit = $totalCredit + $summary[$i]['balance'];
                    }   
                break;
            }
       }
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $result = $ledgerEntryModel->openingBalanceSummary();
        $balance = $result['debit'] - $result['credit'];
        if ($balance > 0) {
            $openingBalance = $balance." Dr";
            
        }
        else {
            $openingBalance = $balance." Cr";
        }
        
            $this->Ln(5);
            $this->Cell(50, 5,"Opening Balance Difference", 1, '0', 'C');
            $this->Cell(50, 5,"$openingBalance", 1, '1', 'C');
            $this->Cell(50, 5,"Total Debit", 1, '0', 'C');
            $this->Cell(50, 5,"$totalDebit", 1, '1', 'C');
            $this->Cell(50, 5,"Total Credit", 1, '0', 'C');
            $this->Cell(50, 5,"$totalCredit", 1, '1', 'C');
            
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

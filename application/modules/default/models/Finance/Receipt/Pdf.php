<?php
require(APPLICATION_PATH . '/../library/fpdf/fpdf.php');
class Core_Model_Finance_Receipt_Pdf extends fpdf
{

    /**
     * @var object invoice model
     */
    protected $_model;
    
    /**
     * @var array invoice meta data
     */
    protected $_receiptData = array();

    /**
     * Set the invoice model
     * @param object invoice model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }
    
    /**
     * @return object Core_Model_Invoice
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Initialize the data
     */
    public function init()
    {
        $this->_receiptData['receiptMetaData'] = $this->getModel()->fetch();
        $receiptMetaData = $this->_receiptData['receiptMetaData'];
        $this->_receiptData['organization'] = new Core_Model_Org;       
        $this->_receiptData['branch'] = new Core_Model_Branch($receiptMetaData['branch_id']);
        $branchModel = $this->_receiptData['branch'];
        $this->_receiptData['branchData'] = $branchModel->fetch();
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
        $this->SetTitle('Receipt');
        $this->writeBillingAddress();

        $this->signature();
    }

    /**
     * Print Logo if exists, Organization Name,Adrress,
     * and Title of the Document
     */
    function Header()
    {
        $receiptId = $this->getModel()->getReceiptId();
        $receiptModel = $this->getModel();
        $receiptMetaData = $this->_receiptData['receiptMetaData'];
    
        $organization = $this->_receiptData['organization'];
        $websiteUrl = $organization->getWebsiteUrl();

        if (is_readable(APPLICATION_PATH . '/data/documents/image/logo.jpg')) {
            $this->Image(APPLICATION_PATH . '/data/documents/image/logo.jpg',
                10,8,0,0,'jpg',$websiteUrl);
        }
        $this->SetFont('Arial','B',10);
        $this->Cell(42);
        
        $this->Cell(110,7,$organization->getName(),0,0,'C',false,'');

        $this->Ln(5);
        $this->SetFont('Arial','',7);
        $branch = $this->_receiptData['branch'];
        $branchData = $this->_receiptData['branchData'];
        $this->Cell(35);

        $branchAddress = $branchData->address_line_1 . ',' . $branchData->address_line_2 . ',' .
                         $branchData->address_line_3 . ',' . $branchData->address_line_4;
        
        $branchLastAddress = $branchData->city . ',' . $branchData->state . ',' .
                    $branchData->postal_code . ',' . $branchData->country . ',' . 
                    $websiteUrl;

        $this->Cell(120, 5, $branchAddress, 0, 1, 'C');
        $this->Ln(-1);
        $this->Cell(35);
        $this->Cell(120, 5, $branchLastAddress, 0, 0, 'C');
            
        $this->SetTextColor(255,0,0);
        $this->SetFont('Arial','B',18);
        $this->Cell(30,7,'RECEIPT',0);
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
     * Display Billing Name and Address table
     * Display Shipping Address table
     * Display Invoice Summary table
     */
    public function writeBillingAddress()
    {
        $receiptId = $this->getModel()->getReceiptId();

        $receiptModel = $this->getModel();
        $receiptMetaData = $this->_receiptData['receiptMetaData'];
    
    
        $toModelHelper = new BV_View_Helper_ReceiptPartyModel();
        $toType = $receiptMetaData['type'];
        $toModel = $toModelHelper->receiptPartyModel($toType, $receiptMetaData['type_id']);
        $toModelData = $toModel->fetch();
    
        $this->SetXY(10,35);
        if (is_a($toModel, 'Core_Model_Contact')) {
            $fullName = $toModel->getFullName();
        } else {
            $fullName = $toModel->getName();
        }
        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);

        $this->SetFont('Arial','',8);
        $this->cell(55, 5,'Receipt To', 1, 1, 'C',1);
        $this->SetTextColor(0,0,0);
        $this->Multicell(55, 4, $fullName , 'LR','L');

        if ($toModelData->billing_address_line_1)
            $this->MultiCell(55,4,$toModelData->billing_address_line_1,'LR');

        if ($toModelData->billing_address_line_2)
            $this->MultiCell(55,4,$toModelData->billing_address_line_2,'LR');

        if ($toModelData->billing_address_line_3)
            $this->MultiCell(55,4,$toModelData->billing_address_line_3,'LR');

        if ($toModelData->billing_address_line_4)
            $this->MultiCell(55,4,$toModelData->billing_address_line_4,'LR');

        if ($toModelData->billing_city)
            $this->MultiCell(55,4,$toModelData->billing_city,'LR');

        if ($toModelData->billing_postal_code)
            $this->MultiCell(55,4,$toModelData->billing_postal_code,'LR');

        if ($toModelData->billing_state)
            $this->MultiCell(55,4,$toModelData->billing_state,'LR');

        if ($toModelData->billing_country)
            $this->MultiCell(55,4,$toModelData->billing_country,'LRB');

        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->SetXY(10,95);
        $this->cell(100,5,'Receipt Summary',1,1,'C',1);

        $this->SetTextColor(0,0,0);
        $date = "Date";
        $this->cell(50,5,$date,1,0);

        $viewHelper = new BV_View_Helper_TimestampToDocument();
        $dateToPrint = $viewHelper->timestampToDocument($receiptMetaData['date']);
        $this->cell(50,5,$dateToPrint,1,1);

        $this->cell(50,5,'Party Name',1,0);
        $this->cell(50,5,$fullName,1,1);

        $this->cell(50,5,'Receipt ID',1,0);
        $this->cell(50,5,$receiptMetaData['receipt_id'] ,1,1);

        $this->cell(50,5,'Amount',1,0);
        $this->cell(50,5,$receiptMetaData['amount'] ,1,1);
       
        $mode = $receiptMetaData['mode'];
        if($mode == Core_Model_Finance_Receipt::DD_CHECK) {
            $recieptBankModel = new Core_Model_Finance_Receipt_Bank;
            $accountId = $receiptMetaData['mode_account_id']; 
            $recieptBankModel->setReceipBanktId($accountId); 
            $bankDetails = $recieptBankModel->fetch();

            $this->cell(50,5,'Branch Name',1,0);
            $this->cell(50,5,$bankDetails['bank_branch'] ,1,1);
            $this->cell(50,5,'Payment Mode',1,0);
            $this->cell(50,5,'DD/CHEQUE' ,1,1);
            $this->cell(50,5,'Account No',1,0);
            $this->cell(50,5,$bankDetails['instrument_number'] ,1,1);
        }elseif ($mode == Core_Model_Finance_Receipt::CASH) {
            $recieptBankModel = new Core_Model_Finance_CashAccount;            
            $cashAccountId = $receiptMetaData['mode_account_id'];
            $recieptBankModel->setCashAccountId($cashAccountId);
            $cashAccountDetails = $recieptBankModel->fetch();

            $this->cell(50,5,'Account Name',1,0);
            $this->cell(50,5,$cashAccountDetails['name'] ,1,1);
            $this->cell(50,5,'Payment Mode',1,0);
            $this->cell(50,5,'CASH' ,1,1);
        }
    }

    /**
     * displaying signature
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

<?php
require(APPLICATION_PATH . '/../library/fpdf/fpdf.php');
class Core_Model_SalesReturn_Pdf extends fpdf
{

    /**
     * @var object salesReturn model
     */
    protected $_model;
    
    /**
     * @var object invoice model
     */
    protected $_invoiceModel;
    
    /**
     * @var string X-axis value Before Printing first Item Name & Details
     */
    protected $_xBeforeItemPrint;

    /**
     * @var string Y-axis value Before Printing first Item Name & Details
     */
    protected $_yBeforeItemPrint;

    /**
     * @var X-axis value After Printing first Item Name & Details
     */
    protected $_xAfterItemPrint;
    
    /**
     * @var Y-axis value After Printing first Item Name & Details
     */
    protected $_yAfterItemPrint;

    /**
     * @var Serial Number for Item Particulars
     */ 
    protected $_serialNumber;

    /**
     * @var Background color enable/disable for Item Particulars Table
     */
    protected $_fill;
    
    /**
     * @var array sales return meta data
     */
    protected $_salesReturnData = array();
    
    /**
     * @var array invoice meta data
     */
    protected $_invoiceData = array();


    /**
     * Set the sales return model
     * @param object sales return model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }
    
    /**
     * @return object Core_Model_SalesReutrn
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Set the invoice model
     * @param object sales return model
     */
    public function setInvoiceModel($model)
    {
        $this->_invoiceModel = $model;
    }
    
    /**
     * @return object Core_Model_Invoice
     */
    public function getInvoiceModel()
    {
        return $this->_invoiceModel;
    }
    
    
    /**
     * Initialize the data
     */
    public function init()
    {
        $this->_salesReturnData['salesReturnMetaData'] = $this->getModel()->fetch();
        $this->_invoiceData['salesReturnMetaData'] = $this->getInvoiceModel()->fetch();
       //var_dump($this->_salesReturnData['salesReturnMetaData']);
       // var_dump($this->_invoiceData['salesReturnMetaData']);
       // exit();
        $salesReturnMetaData = $this->_salesReturnData['salesReturnMetaData'];
        $invoiceMetaData = $this->_invoiceData['salesReturnMetaData'];
        $this->_salesReturnData['organization'] = new Core_Model_Org;       
        $this->_salesReturnData['branch'] = new Core_Model_Branch($invoiceMetaData['branch_id']);
        $branchModel = $this->_salesReturnData['branch'];
        $this->_salesReturnData['branchData'] = $branchModel->fetch();
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
        $this->SetTitle('Credit Note'); 
        $this->writeBillingAddress();
        $this->writeItems();
        $this->amountInWords(); 
        $this->note();
        $this->deliveryTerms();
        $this->paymentTerms();        
        $this->taxInformation();
        $this->signature();
    }

    /**
     * Print Logo if exists, Organization Name,Adrress,
     * and Title of the Document
     */
    function Header()
    {
        $salesReturnId = $this->getModel()->getSalesReturnId();
        $salesReturnModel = $this->getModel();
        $salesReturnMetaData = $this->_salesReturnData['salesReturnMetaData'];
    
        $organization = $this->_salesReturnData['organization'];
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
        $branch = $this->_salesReturnData['branch'];
        $branchData = $this->_salesReturnData['branchData'];
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
        $this->Cell(30,7,'CREDIT NOTE',0);
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
     * Display salesReturn Summary table
     */
    public function writeBillingAddress()
    {
        $salesReturnId = $this->getModel()->getSalesReturnId();
        $salesReturnModel = $this->getModel();
        $salesReturnMetaData = $this->_salesReturnData['salesReturnMetaData'];
        $invoiceMetaData = $this->_invoiceData['salesReturnMetaData'];
        
        $toModelHelper = new BV_View_Helper_InvoicePartyModel();
        $toType = $invoiceMetaData['to_type'];
        $toModel = $toModelHelper->invoicePartyModel($toType, $invoiceMetaData['to_type_id']);
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
        $this->cell(55, 5,'salesReturn To', 1, 1, 'C',1);
        $this->SetTextColor(0,0,0);
        $this->Multicell(55, 4, $fullName , 'LR','L');

        if ($toModelData->billing_address_line_1)
            $this->MultiCell(55,4,$toModelData->billing_address_line_1,'LR');

        if ( $toModelData->billing_address_line_2 )
            $this->MultiCell(55,4,$toModelData->billing_address_line_2,'LR');
    
        if ( $toModelData->billing_address_line_3 )
            $this->MultiCell(55,4,$toModelData->billing_address_line_3,'LR');
        
        if ( $toModelData->billing_address_line_4 )
            $this->MultiCell(55,4,$toModelData->billing_address_line_4,'LR');
        
        if ( $toModelData->billing_city  )
            $this->MultiCell(55,4,$toModelData->billing_city,'LR');

        if ( $toModelData->billing_postal_code )
            $this->MultiCell(55,4,$toModelData->billing_postal_code,'LR');
        
        if ( $toModelData->billing_state )
            $this->MultiCell(55,4,$toModelData->billing_state,'LR');
    
        if ( $toModelData->billing_country )
            $this->MultiCell(55,4,$toModelData->billing_country,'LRB');

        if ($toModelData->tin)
            $this->MultiCell(55,4,"Tin No: $toModelData->tin",'LR');

        if ($toModelData->pan)
            $this->MultiCell(55,4,"PAN No: $toModelData->pan",'LR');

        if ($toModelData->service_tax_number)
            $this->MultiCell(55,4,"Service Tax No: $toModelData->service_tax_number",'LRB');


 
        $this->shippingAddressTable($toModelData);
        
        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->SetXY(10,95);
        $this->cell(100,5,'SalesReturn Summary',1,1,'C',1);
        $this->SetTextColor(0,0,0);
        $date = "Date";
        $this->cell(50,5,$date,1,0);

        $viewHelper = new BV_View_Helper_TimestampToDocument();
        $dateToPrint = $viewHelper->timestampToDocument($salesReturnMetaData['date']);
        
        $this->cell(50,5,$dateToPrint,1,1);
        $this->cell(50,5,'SalesReturn ID',1,0);
        $salesReturnIdToPrint = $this->getInvoiceModel()
                                    ->getSettings()
                                    ->getPrefix(). 
                            $salesReturnMetaData['sales_return_id'] . 
                            $this->getInvoiceModel()
                                    ->getSettings()
                                    ->getSuffix();

        $this->cell(50,5,$salesReturnIdToPrint,1,1);
        $this->cell(50,5,'Customer Contact Person', 1, 0);
        $contactFullName = '';
        $contactEmail = '';
        if (is_numeric($invoiceMetaData['contact_id'])) {
            $contactModel = new Core_Model_Contact($invoiceMetaData['contact_id']);
            $contactRecord = $contactModel->fetch();
            $contactFullName = $contactModel->getFullName();
            $contactEmail = $contactRecord->work_email;
        }
        $this->cell(50,5,$contactFullName,1,1);          
        
        $this->cell(50,10,'Customer E-Mail ID', 1, 0);

        $this->Multicell(50,10,$contactEmail, 1,'L');
        
        $purchaseOrder = $invoiceMetaData['purchase_order'];
        $this->cell(50,5,'Purchase Order', 1, 0);
        $this->cell(50,5,$purchaseOrder, 1, 1);
      
        $this->cell(50, 5, 'Total amount (Rupees)', 1, 0);
        $this->cell(50, 5, number_format($this->getModel()->getTotalAmount(),2,'.',','), 1, 1);
        $this->ln(10);
    }


    /**
     * Write the salesReturn items to the document
     */
    function writeItems()
    {  
        $data = $this->getModel()->getItems();
        $header = array(
            'Serial #',
            'Item',
            'Unit Price (Rs)',
            'Quantity',
            'Tax Type', 
            'Tax (Rs)', 
            'Total (Rs)'
        );

        /**  
         * Set  the colors, line width and bold font
         */
        $this->SetFont('Arial','B',16);
        $this->Cell(190,5,'Item Particulars',0,1,'C',0);
        $this->Ln(2);
        $this->SetFillColor(14,135,241);        
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B',10);

        /**
         * The table header of Item Particulars
         */
        $w = array(20, 60, 25, 20, 25, 20, 20);
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        }
     
        $this->Ln();

        /**
         * Set the color and font restoration
         */
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->_fill = false;
        /**
         * Save XY-axis before displaying first Item Name & Details 
         * then display unit price, Quantity,Tax Type, Tax, Total, Serial Number 
         */
        foreach($data as $row) {            
            $this->_xBeforeItemPrint = $this->GetX();
            $this->_yBeforeItemPrint = $this->GetY();
            $this->printItemData($row);
            $this->_xAfterItemPrint = $this->GetX();
            $this->_yAfterItemPrint = $this->GetY();
            $this->printOtherColumsofIemParticulars($row);
            $this->_fill = !$this->_fill;        
        }
    }
    
    /**  
     * Print Item Name & Details
     */ 
    function printItemData($row)
    {
        $itemNameHelper = new BV_View_Helper_InventoryItemName();
        $itemName = $itemNameHelper->inventoryItemName(
                '1',
                $row['product_id']
            );

        /*$description = $row['item_description'];
        if ($description) {
            $itemDetails = $itemName . "\n" . '(' . $description .')';
        } else {*/
             $itemDetails = $itemName;
       /*  } */
        $this->SetX($this->_xBeforeItemPrint+20);
        $this->SetFillColor(224,235,255);
        $this->MultiCell(60, 5, $itemDetails, 1, 'L', $this->_fill);
    }
    
    /**  
     * Print Unit Price, Quantity, Tax Name,Tax,Total
     */ 
    function printOtherColumsofIemParticulars($row)
    {           
        $this->SetFillColor(224,235,255);
        /**  
         * Get y-axis value before Item Name & Details Printed 
         * and y-axis value after Item Name & Details Printed
         * to find out height of respective row 
         */
        $diff = $this->_yAfterItemPrint - $this->_yBeforeItemPrint; 
        $this->SetXY($this->_xBeforeItemPrint+80, $this->_yBeforeItemPrint);
        $unitPrice = $row['unit_price'];
        $this->MultiCell(25, $diff, number_format($unitPrice,2,'.',','), 1, 'R', $this->_fill);
        
        $this->SetXY($this->_xBeforeItemPrint+105, $this->_yBeforeItemPrint); 
        $quantity = $row['quantity'];
        $this->MultiCell(20, $diff, number_format($quantity,2,'.',','), 1, 'R', $this->_fill);


        $taxHelper = new BV_View_Helper_TaxNameById();
        $taxName = $taxHelper->taxNameById($row['tax_type_id']);

        $this->SetXY($this->_xBeforeItemPrint+125, $this->_yBeforeItemPrint);
        $this->MultiCell(25, $diff, $taxName, 1, 'R', $this->_fill);
        
        $taxPercentageHelper = new BV_View_Helper_TaxPercentageById();
        $taxPercentage = $taxPercentageHelper->taxPercentageById($row['tax_type_id']);
        $beforeTax = $unitPrice * $quantity;
        $tax = ($beforeTax * $taxPercentage) / 100;
        $this->SetXY($this->_xBeforeItemPrint+150, $this->_yBeforeItemPrint);
        $this->MultiCell(20, $diff, number_format($tax,2,'.',','), 1, 'R', $this->_fill);

        $this->SetXY($this->_xBeforeItemPrint+170, $this->_yBeforeItemPrint);
        $lineTotal = $beforeTax + $tax;
        $this->MultiCell(20, $diff, number_format($lineTotal,2,'.',','), 1, 'R', $this->_fill);

        $this->SetXY($this->_xBeforeItemPrint, $this->_yBeforeItemPrint);
        $this->_serialNumber++;
        $this->MultiCell(20, $diff, $this->_serialNumber, 1, 'C', $this->_fill);        
    }
    /**  
     * Print Total amount in words. 
     */
    function amountInWords()
    {
        $this->Ln(5);
        $this->Cell(20, 5, 'Total Amount In Words:',0,0,'L', false);
        $totalAmount = $this->getModel()->getTotalAmount();

        $this->cell(18);
        $viewHelper = new BV_View_Helper_NumberToWord();
        $wordsToPrint = $viewHelper->numberToWord($totalAmount);

        $totalAmountInWords = $wordsToPrint . ' only.';
        $this->Cell(40, 5,$totalAmountInWords,0,0,'L', false);
    }

    /**  
     * Display Shipping Address 
     * @param object array of salesReturn meta data 
     */
    function shippingAddressTable($data)
    {
        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);

        $this->SetXY(80,35);
        $this->Multicell(50, 5,'Ship To',1,'C',1);
        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial','',8);        
        $this->SetX(80);        
       
        if ($data->shipping_address_line_1)
            $this->MultiCell(50,4,$data->shipping_address_line_1,'LR');
        $this->SetX(80);
        
        if ($data->shipping_address_line_2)
            $this->MultiCell(50,4,$data->shipping_address_line_2,'LR');
        $this->SetX(80);
        
        if ($data->shipping_address_line_3)
            $this->Multicell(50,4,$data->shipping_address_line_3,'LR');
        $this->SetX(80);

        if ($data->shipping_address_line_4)
            $this->MultiCell(50,4,$data->shipping_address_line_4,'LR');
        $this->SetX(80);

        if ($data->shipping_city) 
            $this->MultiCell(50,4,$data->shipping_city,'LR');
        $this->SetX(80);
    
        if ($data->shipping_postal_code)
            $this->MultiCell(50,4,$data->shipping_postal_code,'LR');
        $this->SetX(80);
    
        if ($data->shipping_state)
            $this->MultiCell(50,4,$data->shipping_state,'LR');
        $this->SetX(80);
    
        if ($data->shipping_country)
        $this->MultiCell(50,4,$data->shipping_country,'LRB');
    }
    
    /**  
     * Display Note  
     */
    public function note()
    {
        $this->Ln(10);
        $salesReturnId = $this->getModel()->getSalesReturnId();
        $salesReturnModel = $this->getModel();
        $salesReturnMetaData = $salesReturnModel->fetch();
        
        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->SetFont('','B');

        $note = "Note";        
        $this->cell(15, 5, $note, 1, 0,'C','true');
        $this->Ln();
        $note_data = $salesReturnMetaData['notes'];        
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->Multicell(190, 5, $note_data, 1, 'J', 'true');
    }
    
    /**  
     * Display Delivary Terms   
     */
    public function deliveryTerms()
    {
        $invoiceId = $this->getInvoiceModel()->getInvoiceId();
        $invoiceModel = $this->geInvoiceModel();
        $invoiceMetaData = $this->_invoiceData['invoiceMetaData'];
        $this->Ln();

        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->SetFont('','B');

        $delivary_terms = "Delivary Terms";
        $this->cell(30, 5, $delivary_terms, 1, 1, 'C', 'true');
        $delivery_terms = $invoiceMetaData['delivery_terms'];

        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');

        $this->Multicell(190, 5, $delivery_terms, 1, 'J', 'true');
    }

    /**  
     * Display Payment Terms
     */
    public function paymentTerms()
    {
        $invoiceId = $this->getInvoiceModel()->getInvoiceId();
        $invoiceModel = $this->getInvoiceModel();
        $invoiceMetaData = $this->_invoiceData['invoiceMetaData'];
        $this->Ln();
        
        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->SetFont('','B');

        $payment_terms = "Payment Terms";
        $this->cell(30, 5, $payment_terms, 1, 1, 'C', 'true');
        $payment_terms_data = $invoiceMetaData['payment_terms'];

        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');

        $this->Multicell(190, 5, $payment_terms_data, 1, 'J', 'true');
    }
    
    /**  
     * Display Tax Information  
     */
    public function taxInformation()
    {
        $invoiceId = $this->getInvoiceModel()->getInvoiceId();
        $invoiceModel = $this->getInvoiceModel();
        $invoiceMetaData = $this->_invoiceData['invoiceMetaData'];
        
        $branch = new Core_Model_Branch($invoiceMetaData['branch_id']);
        $branchData = $branch->fetch();
        $branchData->service_tax_number;
        $branchData->tin;
        
        $this->Ln(5);
        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->cell(100,5,'Tax Information',1,1,'C',1);
        $this->SetTextColor(0,0,0);
        $this->cell(50,5,"Service Tax Number",1,0);
        $this->cell(50,5,$branchData->service_tax_number,1,1);
        $this->cell(50,5,"Tin Number",1,0);
        $this->cell(50,5,$branchData->tin,1,0);
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

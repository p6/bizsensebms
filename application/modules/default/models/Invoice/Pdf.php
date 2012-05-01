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

class Core_Model_Invoice_Pdf extends fpdf
{

    /**
     * @var object invoice model
     */
    protected $_model;
    
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
     * @var array invoice meta data
     */
    protected $_invoiceData = array();

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
        $this->_invoiceData['invoiceMetaData'] = $this->getModel()->fetch();
        $invoiceMetaData = $this->_invoiceData['invoiceMetaData'];
        $this->_invoiceData['organization'] = new Core_Model_Org;       
        $this->_invoiceData['branch'] = new Core_Model_Branch($invoiceMetaData['branch_id']);
        $branchModel = $this->_invoiceData['branch'];
        $this->_invoiceData['branchData'] = $branchModel->fetch();
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
        $this->SetTitle('Tax Inoivce');
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
        $invoiceId = $this->getModel()->getInvoiceId();
        $invoiceModel = $this->getModel();
        $invoiceMetaData = $this->_invoiceData['invoiceMetaData'];
    
        $organization = $this->_invoiceData['organization'];
        $websiteUrl = $organization->getWebsiteUrl();

        if (is_readable(APPLICATION_PATH . '/data/documents/image/logo.jpg')) {
            $this->Image(APPLICATION_PATH . '/data/documents/image/logo.jpg',
                10,8,30,21,'jpg',$websiteUrl);
        }
        $this->SetFont('Arial','B',16);
        $this->Cell(42);
        
        $this->Cell(110,7,'Binary Vibes Information Technologies Pvt. Ltd',0,0,'C',false,'');

        $this->Ln(7);
        $this->SetFont('Arial','',10);
        $branch = $this->_invoiceData['branch'];
        $branchData = $this->_invoiceData['branchData'];
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
        $this->SetFont('Arial','B',14);
        $this->ln(6);
        
        
        $this->SetFillColor(0,0,0);
        $this->Line(5,30,200,30);
        $this->Ln(6);
       
        $this->Cell(180, 5, 'TAX INVOICE', 0, 0, 'C');
        $this->Ln(26);
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
        $invoiceId = $this->getModel()->getInvoiceId();
        $invoiceModel = $this->getModel();
        $invoiceMetaData = $this->_invoiceData['invoiceMetaData'];
        
        $toModelHelper = new BV_View_Helper_InvoicePartyModel();
        $toType = $invoiceMetaData['to_type'];
        $toModel = $toModelHelper->invoicePartyModel($toType, $invoiceMetaData['to_type_id']);
        $toModelData = $toModel->fetch();
      

        $this->SetXY(10,45); 
        if (is_a($toModel, 'Core_Model_Contact')) {
            $fullName = $toModel->getFullName();     
        } else {
            $fullName = $toModel->getName();
        }
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);

        $this->SetFont('Arial','B',12);  
        $this->cell(55, 5,'Invoice To', 0, 1, 'L',1);
        $this->SetTextColor(0,0,0);
        $this->SetX(15);
        $this->SetFont('Arial','',10); 
        $this->Multicell(155, 4, $fullName , '0','L');
        
        $this->SetX(15);
        if ($toModelData->billing_address_line_1)
            $this->MultiCell(155,4,$toModelData->billing_address_line_1,'0','L');
            
        $this->SetX(15);
        if ( $toModelData->billing_address_line_2 )
            $this->MultiCell(155,4,$toModelData->billing_address_line_2,'0','L');
    
        $this->SetX(15);
        if ( $toModelData->billing_address_line_3 )
            $this->MultiCell(155,4,$toModelData->billing_address_line_3,'0','L');
        
        $this->SetX(15);
        if ( $toModelData->billing_address_line_4 )
            $this->MultiCell(155,4,$toModelData->billing_address_line_4,'0','L');
        
        $this->SetX(15);
        if ( $toModelData->billing_city  )
            $this->MultiCell(155,4,$toModelData->billing_city,'0','L');

        $this->SetX(15);
        if ( $toModelData->billing_postal_code )
            $this->MultiCell(155,4,$toModelData->billing_postal_code,'0','L');
        
        $this->SetX(15);
        if ( $toModelData->billing_state )
            $this->MultiCell(155,4,$toModelData->billing_state,'0','L');
    
        $this->SetX(15);
        if ( $toModelData->billing_country )
            $this->MultiCell(155,4,$toModelData->billing_country,'0','L');
        
        if($toType == Core_Model_Invoice::TO_TYPE_ACCOUNT) {
            if ($toModelData->tin)
                $this->MultiCell(55,4,"Tin No: $toModelData->tin",'0','L');

            if ($toModelData->pan)
                $this->MultiCell(55,4,"PAN No: $toModelData->pan",'0','L');

            if ($toModelData->service_tax_number)
                $this->MultiCell(55,4,"Service Tax No: $toModelData->service_tax_number",'0','L');
                
                
               $x = $this->GetX();
               $this->MultiCell(55,4," ",'LRB');
        }


        if ($toModelData) {
            $this->shippingAddressTable($toModelData);
        }
        
        $viewHelper = new BV_View_Helper_TimestampToDocument();
        $dateToPrint = $viewHelper->timestampToDocument($invoiceMetaData['date']);
        
        $this->SetTextColor(255,255,255);
        $this->SetXY(170,40);
       
        $this->SetTextColor(0,0,0);
        $date = "Date : ";
        $this->cell(10,5,$date,0,0);
        $this->cell(10,5,$dateToPrint,0,1);
        
        $this->SetXY(10,90);
        $this->cell(50,5,'Invoice ID',0,0);
        $invoiceIdToPrint = $this->getModel()
                                    ->getSettings()
                                    ->getPrefix(). 
                            $invoiceMetaData['invoice_id'] . 
                            $this->getModel()
                                    ->getSettings()
                                    ->getSuffix();

        $this->cell(50,5,$invoiceIdToPrint,0,1);
        $this->cell(50,5,'Customer Contact Person', 0, 0);
        $contactFullName = '';
        $contactEmail = '';
        if (is_numeric($invoiceMetaData['contact_id'])) {
            $contactModel = new Core_Model_Contact($invoiceMetaData['contact_id']);
            $contactRecord = $contactModel->fetch();
            $contactFullName = $contactModel->getFullName();
            $contactEmail = $contactRecord->work_email;
        }
        $this->cell(50,5,$contactFullName,0,1);          
        
        $purchaseOrder = $invoiceMetaData['purchase_order'];
        $this->cell(50,5,'Purchase Order', 0, 0);
        $this->cell(50,5,$purchaseOrder, 0, 1);
     
    }


    /**
     * Write the invoice items to the document
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
                Core_Model_Invoice::INVOICE_TYPE_PRODUCT,
                $row['invoice_item_inventory_id']
            );

        $description = $row['item_description'];
        if ($description) {
            $itemDetails = $itemName . "\n" . '(' . $description .')';
        } else {
             $itemDetails = $itemName;
        }
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
        
        $this->cell(40, 5, 'Total amount (Rupees) :', 0, 0);
        $totalAmount = round($this->getModel()->getTotalAmount());
        
        $this->cell(40, 5, number_format($totalAmount,2,'.',','), 0, 1);
        
        $this->Cell(20, 5, 'Total Amount In Words:',0,0,'L', false);
        
        $this->cell(18);
        $viewHelper = new BV_View_Helper_NumberToWord();
        $wordsToPrint = $viewHelper->numberToWord($totalAmount);
              
        $totalAmountInWords = $wordsToPrint . ' only.';
        $this->Cell(40, 5,$totalAmountInWords,0,0,'L', false);
    }

    /**  
     * Display Shipping Address 
     * @param object array of invoice meta data 
     */
    function shippingAddressTable($data)
    {
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);
               
        if ($data->shipping_address_line_1) {
            $this->SetFont('Arial','B',12);    
            $this->SetXY(120,45);
            $this->Multicell(150, 5,'Ship To',0,'L',1);
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial','',10);        
            $this->SetX(130);
            $this->MultiCell(150,4,$data->shipping_address_line_1,'0');
             $this->SetX(130);
        }
       
        if ($data->shipping_address_line_2)
            $this->MultiCell(150,4,$data->shipping_address_line_2,'0');
            $this->SetX(130);
        
        if ($data->shipping_address_line_3)
            $this->Multicell(150,4,$data->shipping_address_line_3,'0');
            $this->SetX(130);

        if ($data->shipping_address_line_4)
            $this->MultiCell(150,4,$data->shipping_address_line_4,'0');
            $this->SetX(130);

        if ($data->shipping_city) 
            $this->MultiCell(150,4,$data->shipping_city,'0');
            $this->SetX(130);
    
        if ($data->shipping_postal_code)
            $this->MultiCell(150,4,$data->shipping_postal_code,'0');
            $this->SetX(130);
    
        if ($data->shipping_state)
            $this->MultiCell(150,4,$data->shipping_state,'0');
        $this->SetX(130);
    
        if ($data->shipping_country)
        $this->MultiCell(50,4,$data->shipping_country,'0');
    }
    
    /**  
     * Display Note  
     */
    public function note()
    {
        $this->Ln(10);
        $invoiceId = $this->getModel()->getInvoiceId();
        $invoiceModel = $this->getModel();
        $invoiceMetaData = $invoiceModel->fetch();
        
        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->SetFont('','B');
        
        $note_data = $invoiceMetaData['notes'];   
        
        if ($note_data) {
            $note = "Note";        
            $this->cell(15, 5, $note, 1, 0,'C','true');
            $this->Ln();    
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('');
            $this->Multicell(190, 5, $note_data, 1, 'J', 'true');
         }
    }
    
    /**  
     * Display Delivary Terms   
     */
    public function deliveryTerms()
    {
        $invoiceId = $this->getModel()->getInvoiceId();
        $invoiceModel = $this->getModel();
        $invoiceMetaData = $this->_invoiceData['invoiceMetaData'];
        $this->Ln();

        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->SetFont('','B');

        $delivery_terms = $invoiceMetaData['delivery_terms'];
        
        if ($delivery_terms) {
            $delivary_terms = "Delivary Terms";
            $this->cell(30, 5, $delivary_terms, 1, 1, 'C', 'true');
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('');
            $this->Multicell(190, 5, $delivery_terms, 1, 'J', 'true');
        }
    }

    /**  
     * Display Payment Terms
     */
    public function paymentTerms()
    {
        $invoiceId = $this->getModel()->getInvoiceId();
        $invoiceModel = $this->getModel();
        $invoiceMetaData = $this->_invoiceData['invoiceMetaData'];
        $this->Ln();
        
        $this->SetFillColor(14,135,241);
        $this->SetTextColor(255,255,255);
        $this->SetFont('','B');
        
        $payment_terms_data = $invoiceMetaData['payment_terms'];
        if ($payment_terms_data) {
            $payment_terms = "Payment Terms";
            $this->cell(30, 5, $payment_terms, 1, 1, 'C', 'true');
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('');
            $this->Multicell(190, 5, $payment_terms_data, 1, 'J', 'true');
        }
    }

     /**  
     * Display Tax Information  
     */
    public function taxInformation()
    {
        $invoiceId = $this->getModel()->getInvoiceId();
        $invoiceModel = $this->getModel();
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

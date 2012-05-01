<?php 
/**
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
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Finance_ReportsController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Tax_Type
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        //$this->_model = new Core_Model_Tax_Type;
    }

    /**
     * Browsable
     */
    public function indexAction()
    {
        
    }
    
    /**
     * trial balance report
     */
    public function trialbalanceAction()
    {
        $form = new Core_Form_Finance_Reports_TrialBalance;
        $action = $this->_helper->url(
                'trialbalance',
                'reports',
                'finance'
        );
        $form->setAction($action);
        $this->view->form = $form;
        
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $total = $ledgerEntryModel->openingBalanceSummary();
        $this->view->total = $total;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $this->getRequest()->getPost();
                $this->view->ledgerId = $data['ledger_id'];
                $ledgerModel = new Core_Model_Finance_Ledger;
                $this->view->balance = $ledgerModel->getTrialBalance(
                                                            $data['ledger_id']); 
            }
        }
        else {
            $ledgerModel = new Core_Model_Finance_Ledger;
            $ledgerId = null;
            $this->view->balance = $ledgerModel->getTrialBalance($ledgerId); 
        }
    }
  
    /**
     * pdf of trail balance 
     */
    public function pdftrialbalanceAction()
    {
        $ledgerId = $this->_getParam('ledger_id');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $ledgerModel = new Core_Model_Finance_Ledger;
        $fileName =  $ledgerModel->getTrialBalancePdfFileLocation($ledgerId);
        $file = file_get_contents($fileName);

        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="trail_balance.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file);
    }

    public function csvexporttrialbalanceAction()
    {
        $ledgerId = $this->_getParam('ledger_id');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $ledgerModel = new Core_Model_Finance_Ledger;
        
        $result = $ledgerModel->getTrialBalance($ledgerId);  
        $totalDebit = 0;
        $totalCredit = 0;
        $file = "Ledger Name , Debit , Credit";
        $file .= PHP_EOL; 
        for($i = 0; $i <= sizeof($result)-1; $i += 1) {
            $file .= PHP_EOL;
            $file .= $result[$i]['ledger_name'].",";
            switch ($result[$i]['balance_type']) {
                case Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_DEBIT :
                    $file .= $result[$i]['balance'].',';
                    $totalDebit = $totalDebit + $result[$i]['balance'];
                break;
                
                case Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_CREDIT :
                    $file .= ",".$result[$i]['balance'];
                    $totalCredit = $totalCredit + $result[$i]['balance'];
                break;
            
                case Core_Model_Finance_Ledger::LEDGER_BALANCE_TYPE_DEBITCREDIT :
                if ($result[$i]['balance'] < 0) {
                    $file .= $result[$i]['balance'].",";
                    $totalDebit = $totalDebit + $result[$i]['balance'];
                }
                else {
                    $file .= ",".$result[$i]['balance'];
                    $totalCredit = $totalCredit + $result[$i]['balance'];
                }   
            break;
            }
       }
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $result = $ledgerEntryModel->openingBalanceSummary();
        $balance = $result['debit'] - $result['credit'];
        $file .= PHP_EOL; 
        if ($balance > 0) {
            $file .= PHP_EOL; 
            $file .= "Opening Balance Difference = ".$balance." Dr";
            $totalDebit += $balance; 
        }
        else {
            $file .= PHP_EOL; 
            $file .= "Opening Balance Difference = ".$balance." Cr";
            $totalCredit += $balance; 
        }
        
        $file .= PHP_EOL; 
        $file .= "Total Debit = ".$totalDebit;
        $file .= PHP_EOL;
        $file .= "Total Credit = ".$totalCredit;
        
        $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 
                                'attachment; filename=trialbalance.csv')
                            ->appendBody($file);
       
       
    }
    
    /**
     * Browsable
     */
    public function daybookAction()
    {
        $form = new Core_Form_Finance_Reports_DayBook;
        $action = $this->_helper->url(
                'daybook',
                'reports',
                'finance'
        );
        $form->setAction($action);
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $this->getRequest()->getPost();
                $this->view->date = $data['date'];
                
                $receiptModel = new Core_Model_Finance_Receipt;
                $this->view->receiptDetails = 
                            $receiptModel->fetchReceiptsByDate($data['date']);
                            
                $paymentModel = new Core_Model_Finance_Payment;
                $this->view->paymentDetails = 
                            $paymentModel->fetchPaymentsByDate($data['date']);
                            
                 $invoiceModel = new Core_Model_Invoice;
                 $this->view->invoiceDetails = 
                            $invoiceModel->fetchInvoiceByDate($data['date']);
                 
                 $salesReturnModel = new Core_Model_SalesReturn;
                 $this->view->salesReturnDetails = 
                    $salesReturnModel->fetchSalesReturnByDate($data['date']);
                    
                 $purchaseModel = new Core_Model_Finance_Purchase;
                 $this->view->purchaseDetails = 
                            $purchaseModel->fetchPurchaseByDate($data['date']);
                            
                 $purchaseReturnModel = new Core_Model_PurchaseReturn;
                 $this->view->purchaseReturnDetails = 
                 $purchaseReturnModel->fetchPurchaseReturnByDate($data['date']);
                 
                 $payslipModel = new Core_Model_Finance_Payslip;
                 $this->view->payslipDetails = 
                 $payslipModel->fetchPayslipByDate($data['date']);
            }
        }
    }
    
    /**
     * day summary CSV export
     */
    public function csvexportdaybookAction()
    {
        $date = $this->_getParam('date');
        $reportModel = new Core_Model_Finance_Report;
        $result = $reportModel->getDayBook($date);   
       
        $file = "Date, Particulars, Type, Id, Debit Amount, Credit Amount";
        for($i = 0; $i <= sizeof($result)-1; $i += 1) {
            $file .= PHP_EOL;
            $file .= $result[$i]['date'].',';
            $file .= $result[$i]['particulars'].',';
            $file .= $result[$i]['type'].',';
            $file .= $result[$i]['id'].',';
            $file .= $result[$i]['debit'].',';
            $file .= $result[$i]['credit'];
        }
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 
                            'attachment; filename=DayBook.csv')
                            ->appendBody($file);
    }

    /**
     * pdf day book summary
     */
    public function pdfdaybookAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $date = $this->_getParam('date');
        $reportModel = new Core_Model_Finance_Report;
        $fileName = $reportModel->getDayBookPdfFileLocation($date);
        $file = file_get_contents($fileName);

        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="day_book.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file);
        
    }
    
    /**
     * group wise summary
     */
    public function groupwisesummaryAction()
    {
        $form = new Core_Form_Finance_Reports_GroupWiseSummary;
        $action = $this->_helper->url(
                'groupwisesummary',
                'reports',
                'finance'
        );
        $form->setAction($action);
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $this->getRequest()->getPost();
                $this->view->groupId = $data['fa_group_id'];
                $ledgerGroupModel = new Core_Model_Finance_Group;
                $this->view->groupRecords = 
                   $ledgerGroupModel->getGroupWiseSummary($data['fa_group_id']); 
            }
        }
        else {
            $ledgerGroupModel = new Core_Model_Finance_Group;
            $groupId = null;
            $this->view->groupRecords = 
                              $ledgerGroupModel->getGroupWiseSummary($groupId); 
        }
        
    }
    
    public function csvexportgroupwisesummaryAction()
    {
       $groupId = $this->_getParam('group_id');
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       
       $ledgerGroupModel = new Core_Model_Finance_Group;
       $result = $ledgerGroupModel->getGroupWiseSummary($groupId); 
       
       $file = "Group Name , Balance";
       $file .= PHP_EOL;
       foreach ($result as $groupName => $balance) {
         $file .= PHP_EOL;
         $file .= $groupName.",".$balance;
       }
       
       $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 
                            'attachment; filename=LedgerWiseSummary.csv')
                            ->appendBody($file);
    }
    
    /**
     * ledger wise summary
     */
    public function ledgerwisesummaryAction()
    {
        $form = new Core_Form_Finance_Reports_TrialBalance;
        $action = $this->_helper->url(
                'trialbalance',
                'reports',
                'finance'
        );
        $form->setAction($action);
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $this->getRequest()->getPost();
                $this->view->ledgerId = $data['ledger_id'];
                $ledgerModel = new Core_Model_Finance_Ledger;
                $this->view->summary = $ledgerModel->getSummary($data['ledger_id']); 
            }
        }
        else {
            $ledgerModel = new Core_Model_Finance_Ledger;
            $ledgerId = null;
            $this->view->summary = $ledgerModel->getSummary($ledgerId);  
        }
        
    }
    
    /**
     * ledger wise summary CSV export
     */
    public function csvexportledgerwisesummaryAction()
    {
       $ledgerId = $this->_getParam('ledger_id');
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       
       $ledgerModel = new Core_Model_Finance_Ledger;
       $result = $ledgerModel->getSummary($ledgerId);  
       
       $file = "Ledger Name, Balance";
       $file .= PHP_EOL;
       for($i = 0; $i <= sizeof($result)-1; $i += 1) {
           $file .= PHP_EOL;
           $file .= $result[$i]['ledger_name'].",";
           $totalBalance = $result[$i]['balance'];
           if ($totalBalance > 0) {
              $file .= $totalBalance." Cr".","; 
           }
           else {
              $file .= abs($totalBalance)." Dr".","; 
           }
       }
       
       $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 
                            'attachment; filename=LedgerWiseSummary.csv')
                            ->appendBody($file);
    }
  
    /**
     * pdf Ledger Wise Summary
     */
    public function pdfexportAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $ledgerModel = new Core_Model_Finance_Ledger;
        $fileName =  $ledgerModel->getPdfFileLocation();
        $file = file_get_contents($fileName);
    
        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="ledger_wise.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file); 
    }

    /**
     * pdf Sales Registry
     */
    public function pdfsalesregisterAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
       
        $invoiceModel = new Core_Model_Invoice;
        $invoiceRecord =  $invoiceModel->salesRegister();
       
        $salesReturnModel = new Core_Model_SalesReturn;
        $salesReturnRecord  = $salesReturnModel->salesReturnRegister();
       
        $result = array_merge($invoiceRecord, $salesReturnRecord);
        
        $pdf = new Core_Model_Invoice_Sales_Pdf_Create();
        $pdf->setSummaryDetails($result);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/documents/reports/sales_registry_summary' . '.pdf';
        $pdf->Output($pdfPath, 'F');
        $file = file_get_contents($pdfPath);
    
        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="sales_register.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file); 
    }

    /**
     * Sales Registry
     */
    public function salesregisterAction()
    {
        $form = new Core_Form_Finance_Reports_SalesRegister;
        $action = $this->_helper->url(
                'salesregister',
                'reports',
                'finance'
        );
        $form->setAction($action);
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $this->getRequest()->getPost();
                $invoiceModel = new Core_Model_Invoice;
                $this->view->date = $data['date'];
                if ($data['date'] != '') {
                    $this->view->invoiceDetails = 
                            $invoiceModel->fetchInvoiceByDate($data['date']);
                    $salesReturnModel = new Core_Model_SalesReturn;
                    $this->view->salesReturnDetails = 
                      $salesReturnModel->fetchSalesReturnByDate($data['date']); 
               }
               else {
                    $invoiceModel = new Core_Model_Invoice;
                    $this->view->invoiceDetails = $invoiceModel->fetchAll();
                    $salesReturnModel = new Core_Model_SalesReturn;
                    $this->view->date = null;
                    $this->view->salesReturnDetails = $salesReturnModel->fetchAll();
               }
            }
        }
        else {
            $invoiceModel = new Core_Model_Invoice;
            $this->view->invoiceDetails = $invoiceModel->fetchAll();
            $salesReturnModel = new Core_Model_SalesReturn;
            $this->view->date = null;
            $this->view->salesReturnDetails = $salesReturnModel->fetchAll();
        }
    }
    
    /**
     * sales register CSV export
     */
    public function csvexportsalesregistryAction()
    {
       $date = $this->_getParam('date');
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       
       $invoiceModel = new Core_Model_Invoice;
       $invoiceRecord =  $invoiceModel->salesRegister($date);
       
       $salesReturnModel = new Core_Model_SalesReturn;
       $salesReturnRecord = $salesReturnModel->salesReturnRegister();
              
       $file = "Date, Particulars, Type, Id, Debit Amount, Credit Amount";
       $file .= PHP_EOL;
       $result = array_merge($invoiceRecord, $salesReturnRecord);
       
       for($i = 0; $i <= sizeof($result)-1; $i += 1) {
           $file .= PHP_EOL;
           $file .= $result[$i]['date'].",";
           $file .= $result[$i]['particulars'].",";
           $file .= $result[$i]['type'].",";
           $file .= $result[$i]['id'].",";
           $file .= $result[$i]['total_amount'].",";
           foreach ($result[$i]['tax'] as $taxName => $amount) {
               $file .= $taxName."-".$amount;
           }
       }
       
       $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 
                            'attachment; filename=SalesRegistry.csv')
                            ->appendBody($file);
    }
    
    /**
     * Purchase Registry
     */
    public function purchaseregisterAction()
    {
        $form = new Core_Form_Finance_Reports_SalesRegister;
        $action = $this->_helper->url(
                'purchaseregister',
                'reports',
                'finance'
        );
        $form->setAction($action);
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $this->getRequest()->getPost();
                $purchaseModel = new Core_Model_Finance_Purchase;
                $this->view->purchaseDetails = 
                        $purchaseModel->fetchPurchaseByDate($data['date']);
                $purchaseReturnModel = new Core_Model_PurchaseReturn;
                $this->view->purchaseReturnDetails = 
                 $purchaseReturnModel->fetchPurchaseReturnByDate($data['date']);
                
            }
        }
        else {
            $purchaseModel = new Core_Model_Finance_Purchase;
            $this->view->purchaseDetails = $purchaseModel->fetchAll();
            $purchaseReturnModel = new Core_Model_PurchaseReturn;
            $this->view->purchaseReturnDetails = $purchaseReturnModel->fetchAll();
        }
    }
    
    /**
     * purchase register summary CSV export
     */
    public function csvexportpurchaseregistryAction()
    {
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       
       $purchaseModel = new Core_Model_Finance_Purchase;
       $purchaseRecord =  $purchaseModel->purchaseRegister();
       
       $purchaseReturnModel = new Core_Model_PurchaseReturn;
       $purchaseReturnRecord  = $purchaseReturnModel->purchaseReturnRegister();
       
       $file = "Date, Particulars, Type, Id, Debit Amount, Credit Amount";
       $file .= PHP_EOL;
       $result = array_merge($purchaseRecord, $purchaseReturnRecord);
       
       for($i = 0; $i <= sizeof($result)-1; $i += 1) {
           $file .= PHP_EOL;
           $file .= $result[$i]['date'].",";
           $file .= $result[$i]['particulars'].",";
           $file .= $result[$i]['type'].",";
           $file .= $result[$i]['id'].",";
           $file .= $result[$i]['total_amount'].",";
           foreach ($result[$i]['tax'] as $taxName => $amount) {
               $file .= $taxName."-".$amount;
           }
           
       }
       
       $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 
                            'attachment; filename=PurchaseRegistry.csv')
                            ->appendBody($file);
    }

    /**
     * pdf Purchase Registry
     */
    public function pdfpurchaseregistryAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
      
        $purchaseModel = new Core_Model_Finance_Purchase;
        $purchaseRecord =  $purchaseModel->purchaseRegister();
       
        $purchaseReturnModel = new Core_Model_PurchaseReturn;
        $purchaseReturnRecord  = $purchaseReturnModel->purchaseReturnRegister();
        $result = array_merge($purchaseRecord, $purchaseReturnRecord);

        $pdf = new Core_Model_Finance_Purchase_Pdf_Create();
        $pdf->setSummaryDetails($result);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/documents/reports/purchase_registry_summary' . '.pdf';
        $pdf->Output($pdfPath, 'F');
        $file = file_get_contents($pdfPath);
    
        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="sales_register.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file); 
    }

    /**
     * outstandings
     */
    public function outstandingsAction()
    {
        
    }
    
    /**
     *  outstandings CSV export
     */
    public function csvexportoutstandingsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);  
        
        $ledgerModel = new Core_Model_Finance_Ledger;
        $sundryDebtors = $ledgerModel->getLedgerBalanceByGroup(
                                                    'Sundry Debtors');
        $sundryCreditors = $ledgerModel->getLedgerBalanceByGroup(
                                                  'Sundry Creditors');
                
        $file  = "From, Sundry, Debtors";
        $file .= PHP_EOL;
        $file .= "Ledger Name, Balance";
        foreach ($sundryDebtors as $ledgerName => $balance) {
            $file .= PHP_EOL;
            $file .= $ledgerName.",";
            if ($balance > 0) {
                $file .= $balance." Cr"; 
            }
            else {
                $file .= abs($balance)." Dr"; 
            }
         }
        $file .= PHP_EOL;
        $file .= PHP_EOL;
        $file .= PHP_EOL;
        $file .= "From, Sundry, Creditors";
        $file .= PHP_EOL;
        $file .= "Ledger Name, Balance";
        foreach ($sundryCreditors as $ledgerName => $balance) {
            $file .= PHP_EOL;
            $file .= $ledgerName.",";
            if ($balance > 0) {
                $file .= $balance." Cr"; 
            }
            else {
                $file .= abs($balance)." Dr"; 
            }
         }
         
         $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 
                            'attachment; filename=outstandings.csv')
                            ->appendBody($file);
    }

    /**
     * pdf outstandings summary
     */
    public function pdfoutstandingsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $ledgerModel = new Core_Model_Finance_Ledger;
        $sundryDebtors = $ledgerModel->getLedgerBalanceByGroup(
                                                    'Sundry Debtors');
        $sundryCreditors = $ledgerModel->getLedgerBalanceByGroup(
                                                  'Sundry Creditors');
 
        $pdf = new Core_Model_Finance_Ledger_Pdf_Outstanding();
        $pdf->setSummaryDetails($sundryDebtors, $sundryCreditors);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/documents/reports/outstanding_summary' . '.pdf';
        $pdf->Output($pdfPath, 'F');
        $file = file_get_contents($pdfPath);
    
        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="sales_register.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file); 
    }
    
    /**
     * ledgers by group
     */
    public function ledgersbygroupAction()
    {
        $groupName = $this->_getParam('group_name');
        $ledgerModel = new Core_Model_Finance_Ledger;
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $ledgersRecord = $ledgerModel->fetchByGroup($groupName);
        
        $totalBalance = 0;
        $result = array();
        for($x = 0; $x <= sizeof($ledgersRecord)-1; $x += 1) {
            $temp['name'] = $ledgersRecord[$x]['name'];
            $temp['fa_ledger_id'] = $ledgersRecord[$x]['fa_ledger_id'];
            $temp['balance'] = $ledgerEntryModel->getBalanceByLedgerId(
                                          $ledgersRecord[$x]['fa_ledger_id']);
            $result[] = $temp;
            $totalBalance += $temp['balance'];
          }
        $this->view->ledgerDetails = $result;
        $this->view->totalBalance = $totalBalance;
        $this->view->groupName = $groupName;
        
    }
    
    /**
     * ledgers by group
     */
    public function openingbalancesummaryAction()
    {
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $total = $ledgerEntryModel->openingBalanceSummary();
        $this->view->total = $total;
    }
} 

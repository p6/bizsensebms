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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 */

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Finance_Report extends Core_Model_Abstract
{
    /**
     * @param date 
     * @return array 
     */
    public function getDayBook($date) 
    {
        $result = array();
        $temp = array();
        if (!$date) {
           return $result;
        }
        $timeHelper = new BV_View_Helper_TimestampToDocument;
        /**
         * Receipt
         */
        $receiptModel = new Core_Model_Finance_Receipt;
        $receiptDetails = $receiptModel->fetchReceiptsByDate($date);
        $invoiceTemp = array();
        for($i = 0; $i <= sizeof($receiptDetails)-1; $i += 1) {
            $invoiceTemp['date'] = $timeHelper->timestampToDocument(
                                                $receiptDetails[$i]['date']);
                       
            if($receiptDetails[$i]['type_id'] != null) {
                   if ($toType == Core_Model_Invoice::TO_TYPE_ACCOUNT) {
                        $accountModel = new Core_Model_Account(
                                            $receiptDetails[$i]['type_id']);
                        $invoiceTemp['particulars'] = $accountModel->getName();
                    } else {
                        $contactModel = new Core_Model_Contact(
                                            $receiptDetails[$i]['type_id']);
                        $invoiceTemp['particulars'] = $contactModel->getFullName();
                    }
                }
                else {
                   if ($receiptDetails[$i]['mode'] == 0) {
                       $cashAccountModel = new Core_Model_Finance_CashAccount(
                                        $receiptDetails[$i]['mode_account_id']);
                       $invoiceTemp['particulars'] = $cashAccountModel->getName();
                    }
                    else {
                       $bankaccountModel = new Core_Model_Finance_BankAccount(
                                        $receiptDetails[$i]['mode_account_id']);
                       $bank = $bankaccountModel->getBankName();
                       $bank .= "-".$bankaccountModel->getAccountNumber();
                       $invoiceTemp['particulars'] = $bank;
                    }
                }
                        
            $invoiceTemp['type'] = "Receipts";
            
            $invoiceTemp['id'] = $receiptDetails[$i]['receipt_id'];
        
            $invoiceTemp['debit'] = $receiptDetails[$i]['amount'];
        
            $invoiceTemp['credit'] = '0';
        } 
        
        if ($invoiceTemp) {
            $result[] = $invoiceTemp;
        }
        
        /**
         * Payment
         */              
        $paymentModel = new Core_Model_Finance_Payment;
        $paymentDetails = $paymentModel->fetchPaymentsByDate($date);
        $paymentTemp = array();
        for($i = 0; $i <= sizeof($paymentDetails)-1; $i += 1) { 
            $timeHelper = new BV_View_Helper_TimestampToDocument;
            $paymentTemp['date'] = $timeHelper->timestampToDocument(
                                                $paymentDetails[$i]['created']);
            if ($paymentDetails[$i]['mode'] == 1 &&  $paymentDetails[$i]['type'] == 1) { 
                $vendorModel = new Core_Model_Finance_Vendor(
                                                    $paymentDetails[$i]['type_id']);
                $paymentTemp['particulars'] = "Vendor Name = ". 
                                                    $vendorModel->getName();
             } 
                       
             if ($paymentDetails[$i]['mode'] == 2 && $paymentDetails[$i]['type'] == 1) {
                $vendorModel = new Core_Model_Finance_Vendor(
                                                    $paymentDetails[$i]['type_id']);
                $paymentTemp['particulars'] = "Vendor Name = ". 
                                                    $vendorModel->getName();
             
               } 
              
             if ($paymentDetails[$i]['mode'] == 1 &&  $paymentDetails[$i]['type'] == 2) { 
                $vendorModel = new Core_Model_Finance_Vendor(
                                                    $paymentDetails[$i]['type_id']);
                $paymentTemp['particulars'] = "Vendor Name = ". 
                                                    $vendorModel->getName();
              }  
             
              if ($paymentDetails[$i]['mode'] == 2 && $paymentDetails[$i]['type'] == 2) {
                 $vendorModel = new Core_Model_Finance_Vendor(
                                                    $paymentDetails[$i]['type_id']);
                 $paymentTemp['particulars'] = "Vendor Name = ". 
                                                    $vendorModel->getName(); 
                
               } 
            
               if ($paymentDetails[$i]['mode'] == 1 && $paymentDetails[$i]['type'] == 3) {
                  $paymentTemp['particulars'] = 'TDS Cash';
               } 
               
               if ($paymentDetails[$i]['mode'] == 2 && $paymentDetails[$i]['type'] == 3) {
                  $paymentTemp['particulars'] = 'TDS Cheque';
               } 
             
               if ($paymentDetails[$i]['mode'] == 1 && $paymentDetails[$i]['type'] == 4) {
                    $cashAccountModel = new Core_Model_Finance_CashAccount(
                                                      $paymentDetails[$i]['mode_id']);
                    $output = $cashAccountModel->getName();
                    $paymentTemp['particulars'] = 'Cash Account - '.$output;
               } 
             
               if ($paymentDetails[$i]['mode'] == 2 && $paymentDetails[$i]['type'] == 4) {
                    $bankaccountModel = new Core_Model_Finance_BankAccount(
                                                    $paymentDetails[$i]['mode_id']);
                    $bank = $bankaccountModel->getBankName();
                    $bank .= "-".$bankaccountModel->getAccountNumber();
                    $paymentTemp['particulars'] = 'Bank Account - '.$bank;
                } 
             
             
                if ($paymentDetails[$i]['mode'] == 1 && $paymentDetails[$i]['type'] == 5) {
                    $user = new Core_Model_User($paymentDetails[$i]['type_id']);
                    $fullName = $user->getProfile()->getFullName();
                    $paymentTemp['particulars'] = 'Salaray to - '.$fullName;
                } 
             
             
                if ($paymentDetails[$i]['mode'] == 2 && $paymentDetails[$i]['type'] == 5) {
                    $user = new Core_Model_User($paymentDetails[$i]['type_id']);
                    $fullName = $user->getProfile()->getFullName();
                    $paymentTemp['particulars'] = 'Salaray to - '.$fullName;           
                } 
             
            $paymentTemp['type'] = "Payments"; 
            
            $paymentTemp['id'] = $paymentDetails[$i]['payment_id'];
            
            $paymentTemp['debit'] = '0';
        
            $paymentTemp['credit'] = $paymentDetails[$i]['amount'];
                  
        }  
        
        if ($paymentTemp) {
            $result[] = $paymentTemp;
        }
        
        /**
         * Invoice
         */
        $invoiceModel = new Core_Model_Invoice;
        $invoiceDetails = $invoiceModel->fetchInvoiceByDate($date);
        $invoiceTemp = array();
        for($i = 0; $i <= sizeof($invoiceDetails)-1; $i += 1) {
            $invoiceTemp['date'] = $timeHelper->timestampToDocument(
                                                $invoiceDetails[$i]['date']);
             if ($invoiceDetails[$i]['to_type'] == 
                                       Core_Model_Invoice::TO_TYPE_ACCOUNT) {
                    $accountModel = new Core_Model_Account(
                                            $invoiceDetails[$i]['to_type_id']);
                    $invoiceTemp['particulars'] = $accountModel->getName();
            } else {
                    $contactModel = new Core_Model_Contact(
                                            $invoiceDetails[$i]['to_type_id']);
                    $invoiceTemp['particulars'] = $contactModel->getFullName();
            }
             
            $invoiceTemp['type'] = "Invoice"; 
            
            $invoiceTemp['id'] = $invoiceDetails[$i]['invoice_id'];
            
            $invoiceTemp['debit'] = '0';   
            
            $invoiceModel = new Core_Model_Invoice(
                                    $invoiceDetails[$i]['invoice_id']);
            $invoiceTemp['credit'] = $invoiceModel->getTotalAmount();
        }
        
        if ($invoiceTemp) {
            $result[] = $invoiceTemp;
        }
        
        /**
         * Sales Return
         */ 
        $salesReturnModel = new Core_Model_SalesReturn;
        $salesReturnDetails = $salesReturnModel->fetchSalesReturnByDate($date);
        $salesReturnTemp = array();
        for($i = 0; $i <= sizeof($salesReturnDetails)-1; $i += 1) {
            $salesReturnTemp['date'] = $timeHelper->timestampToDocument(
                                              $salesReturnDetails[$i]['date']);          
            $invoiceModel = new Core_Model_Invoice(
                        $salesReturnDetails[$i]['invoice_id']);
            $invoiceRecord = $invoiceModel->fetch();
            if ($invoiceRecord['to_type'] == 
                    Core_Model_Invoice::TO_TYPE_ACCOUNT) {
                    $accountModel = new Core_Model_Account(
                                            $invoiceRecord['to_type_id']);
                    $salesReturnTemp['particulars'] = $accountModel->getName();
            } else {
                    $contactModel = new Core_Model_Contact(
                                            $invoiceRecord['to_type_id']);
                    $salesReturnTemp['particulars'] = 
                                                  $contactModel->getFullName();
            }
            $salesReturnTemp['type'] = "Sales Return"; 
            
            $salesReturnTemp['id'] = $salesReturnDetails[$i]['sales_return_id'];
            
            $salesReturnModel = new Core_Model_SalesReturn(
                        $salesReturnDetails[$i]['sales_return_id']);
            $salesReturnTemp['debit'] = $salesReturnModel->getTotalAmount();
        
            $salesReturnTemp['credit'] = '0';   
                           
        }
        
        if ($salesReturnTemp) {
            $result[] = $salesReturnTemp;
        }
        
        /**
         * Purchase
         */ 
        $purchaseModel = new Core_Model_Finance_Purchase;
        $purchaseDetails = $purchaseModel->fetchPurchaseByDate($date);
        $purchaseTemp = array();
        for($p = 0; $p <= sizeof($purchaseDetails)-1; $p += 1) {
            $purchaseTemp['date'] = $timeHelper->timestampToDocument(
                                              $purchaseDetails[$p]['date']); 
                                              
            $vendorModel = new Core_Model_Finance_Vendor(
                                        $purchaseDetails[$p]['vendor_id']);
            $purchaseTemp['particulars'] = $vendorModel->getName();
            
            $purchaseTemp['type'] = "Purchase"; 
            
            $purchaseTemp['id'] =  $purchaseDetails[$p]['purchase_id'];
            
            $purchaseModel = new Core_Model_Finance_Purchase(
                                    $purchaseDetails[$p]['purchase_id']);
            $purchaseTemp['debit'] = $purchaseModel->getTotalAmount();
        
            $purchaseTemp['credit'] = '0';
            
        }
        
        if ($purchaseTemp) {
            $result[] = $purchaseTemp;
        }
        
        /**
         * purchase Return
         */                  
        $purchaseReturnModel = new Core_Model_PurchaseReturn;
        $purchaseReturnDetails = 
                $purchaseReturnModel->fetchPurchaseReturnByDate($date);
        $purchaseReturnTemp = array();
        for($pr = 0; $pr <= sizeof($purchaseReturnDetails)-1; $pr += 1) {
            $purchaseReturnTemp['date'] = $timeHelper->timestampToDocument(
                                         $purchaseReturnDetails[$pr]['date']); 
                       
            $purchaseModel = new Core_Model_Finance_Purchase(
                                    $purchaseReturnDetails[$pr]['purchase_id']);
            $purchaseReturnRecord = $purchaseModel->fetch();
            $vendorModel = new Core_Model_Finance_Vendor(
                                        $purchaseReturnRecord['vendor_id']);
            $purchaseReturnTemp['particulars'] = $vendorModel->getName();
            
            $purchaseReturnTemp['type'] = "Purchase Return"; 
            
            $purchaseReturnTemp['id'] =  $purchaseReturnDetails[$pr]['purchase_return_id'];
            
            $purchaseReturnTemp['debit'] = '0';
            
            $purchaseReturnModel = new Core_Model_PurchaseReturn(
                        $purchaseReturnDetails[$pr]['purchase_return_id']);
            $purchaseReturnTemp['credit'] = $purchaseReturnModel->getTotalAmount();
            
        } 
        
        if ($purchaseReturnTemp) {
            $result[] = $purchaseReturnTemp;
        }
        
        /**
         * Payslip
         */                                            
        $payslipModel = new Core_Model_Finance_Payslip;
        $payslipDetails = $payslipModel->fetchPayslipByDate($date);
        $payslipTemp = array();
        for($p = 0; $p <= sizeof($payslipDetails)-1; $p += 1) {
            $payslipTemp['date'] = $timeHelper->timestampToDocument(
                                         $payslipDetails[$p]['date']); 
           
            $user = new Core_Model_User($payslipDetails[$p]['employee_id']);
            $payslipTemp['particulars'] = $user->getProfile()->getFullName(); 
            
            $payslipTemp['type'] = "Payslip"; 
            
            $payslipTemp['id'] =  $payslipDetails[$p]['payslip_id'];
            
            $payslipTemp['debit'] = '0';
            
            $payslipModel = new Core_Model_Finance_Payslip(
                                    $payslipDetails[$p]['payslip_id']);
            $payslipTemp['credit'] = $payslipModel->getPayableSalaryAmount();
        }
        
        if ($payslipTemp) {
            $result[] = $payslipTemp;
        }
        
        return $result;
    }

    /**
     * @paran date $date 
     * $return string path
     */
    public function getDayBookPdfFileLocation($date)
    {
        $summary = $this->getDayBook($date);
        $pdf = new Core_Model_Finance_Report_Pdf_DayBook();
        $pdf->setSummaryDetails($summary);
        $pdf->run();
        $pdfPath = APPLICATION_PATH
                     . '/data/documents/reports/day_book_summary' . '.pdf';
        $pdf->Output($pdfPath, 'F');
        return $pdfPath;
    }

}



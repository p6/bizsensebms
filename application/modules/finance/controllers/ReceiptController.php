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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Finance_ReceiptController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Receipt
     */
    protected $_model;
    
     /**
     * @var object Core_Model_ReceiptBank
     */
    protected $_bankModel;

    /**
     * Initialize the controller
     */
    function init()
    {
        $this->_model = new Core_Model_Finance_Receipt;
        $this->_bankModel = new Core_Model_Finance_Receipt_Bank;
    }

    /**
     * Browsable, sortable, searchable list of Receipts
     */
    public function indexAction()
    {
       $form = new Core_Form_Finance_Receipt_Search;
       $form->populate($_GET);
       $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
        
       $paginator = $this->_model->getPaginator($form->getValues(), 
                                                     $this->_getParam('sort'));  
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
    }
    
    /**
     * Create a new Sundry Debtors Cash Receipt
     *
     * @see Zend_Controller_Action
     */ 
    public function cashreceiptAction()
    {
       $form = new Core_Form_Finance_Receipt_Create;
        $form->setAction(
            $this->_helper->url(
                'cashreceipt',
                'receipt',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createCashReceipt(
                                               $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                          'The cash receipt was created successfully');
                $this->_helper->Redirector('index');
            }
        }
        
    }
    
    /**
     * Create a new Indirect Income Cash Receipt
     *
     * @see Zend_Controller_Action
     */ 
    public function icashreceiptAction()
    {
       $form = new Core_Form_Finance_Receipt_IndirectIncomeCashReceipt;
        $form->setAction(
            $this->_helper->url(
                'icashreceipt',
                'receipt',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createIndirectIncomeCashReceipt(
                                            $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                'The cash receipt was created successfully');
                $this->_helper->Redirector('index');
            }
        }
        
    }
    
    /**
     * Create a new Sundry Debtors Cheque Receipt
     *
     * @see Zend_Controller_Action
     */ 
    public function chequereceiptAction()
    {
        $form = new Core_Form_Finance_Receipt_CreateReceipt;
        $form->setAction(
            $this->_helper->url(
                'chequereceipt',
                'receipt',
                'finance'
            )
        );
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createChequeReceipt(
                                            $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                             'The cheque receipt was created successfully');
                $this->_helper->Redirector('index');
            }
        }
        
    }
    
    /**
     * Create a new Indirect Income Cheque Receipt
     *
     * @see Zend_Controller_Action
     */ 
    public function ichequereceiptAction()
    {
        $form = new Core_Form_Finance_Receipt_IndirectIncomeChequeReceipt;
        $form->setAction(
            $this->_helper->url(
                'ichequereceipt',
                'receipt',
                'finance'
            )
        );
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createIndirectIncomeChequeReceipt(
                                        $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                           'The cheque receipt was created successfully');
                $this->_helper->Redirector('index');
            }
        }
        
    }
    
    /**
     * View the details of the Receipt
     */
    public function detailsAction()
    {
        $receiptId = $this->_getParam('receipt_id'); 
        $this->_model->setReceiptId($receiptId);
        $this->view->receipt = $this->_model->fetch();
        $this->view->receiptbank =
                             $this->_bankModel->fetchByReceiptId($receiptId);
        $this->view->receiptId = $receiptId;	
    }
    
    /**
     * View the details of the Cash Receipt
     */
    public function cashreceiptdetailsAction()
    {
        $receiptId = $this->_getParam('receipt_id'); 
        $this->_model->setReceiptId($receiptId);
        $this->view->receipt = $this->_model->fetch();
        $this->view->receiptId = $receiptId;	
    }
    
    /**
     * Edit a Sundry Debtors Cheque Receipt
     */
    public function editAction()
    {
        $receiptId = $this->_getParam('receipt_id'); 
        $this->_model->setReceiptId($receiptId);
        
        $form = new Core_Form_Finance_Receipt_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'receipt', 
                'finance',
                array(
                    'receipt_id'=>$receiptId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger(
                           'The receipt has been edited successfully');
                $this->_helper->redirector('index', 'receipt', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $defaultValues = $this->_model->fetch();
            $date = new Zend_Date();
            $defaultValues['date'] = $date->setTimestamp($defaultValues['date']);
            $defaultValues['date'] = $this->view->timestampToDojo($defaultValues['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Edit a Sundry Debtors Cash Receipt
     */
    public function editcashreceiptAction()
    {
        $receiptId = $this->_getParam('receipt_id'); 
        $this->_model->setReceiptId($receiptId);
        
        $form = new Core_Form_Finance_Receipt_EditCashReceipt($this->_model);
        $form->setAction($this->_helper->url(
                'editcashreceipt', 
                'receipt', 
                'finance',
                array(
                    'receipt_id'=>$receiptId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editCashReceipt($form->getValues());
                $this->_helper->FlashMessenger(
                              'The cash receipt has been edited successfully');
                $this->_helper->redirector('index', 'receipt', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $defaultValues = $this->_model->fetch();
            $date = new Zend_Date();
            $defaultValues['date'] = $date->setTimestamp($defaultValues['date']);
            $defaultValues['date'] = $this->view->timestampToDojo($defaultValues['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Edit a Indirect Income Cheque Receipt
     */
    public function editichequereceiptAction()
    {
        $receiptId = $this->_getParam('receipt_id'); 
        $this->_model->setReceiptId($receiptId);
        
        $form = new Core_Form_Finance_Receipt_EditIndirectIncomeChequeReceipt($this->_model);
        $form->setAction($this->_helper->url(
                'editichequereceipt', 
                'receipt', 
                'finance',
                array(
                    'receipt_id'=>$receiptId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editIndirectIncomeChequeReceipt($form->getValues());
                $this->_helper->FlashMessenger(
                           'The cheque receipt has been edited successfully');
                $this->_helper->redirector('index', 'receipt', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }
    
     /**
     * Edit a Indirect Income Cash Receipt
     */
    public function editicashreceiptAction()
    {
        $receiptId = $this->_getParam('receipt_id'); 
        $this->_model->setReceiptId($receiptId);
        
        $form = new Core_Form_Finance_Receipt_EditIndirectIncomeCashReceipt($this->_model);
        $form->setAction($this->_helper->url(
                'editicashreceipt', 
                'receipt', 
                'finance',
                array(
                    'receipt_id'=>$receiptId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editIndirectIncomeCashReceipt($form->getValues());
                $this->_helper->FlashMessenger(
                           'The cash receipt has been edited successfully');
                $this->_helper->redirector('index', 'receipt', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }
    
    /**
     * Delete the Cash account
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setReceiptId($this->_getParam('receipt_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The receipt was successfully deleted'; 
        } else {
           $message = 'The receipt could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'receipt', 'finance');
    
    }
    
    /**
     * View the details of the Indirect Income Cheque Receipt
     */
    public function ichequereceiptdetailsAction()
    {
        $receiptId = $this->_getParam('receipt_id'); 
        $this->_model->setReceiptId($receiptId);
        $this->view->receipt = $this->_model->fetch();
        $this->view->receiptbank = 
                            $this->_bankModel->fetchByReceiptId($receiptId);
        $this->view->receiptId = $receiptId;	
    }
    
    /**
     * View the details of the Indirect Income Cash Receipt
     */
    public function icashreceiptdetailsAction()
    {
        $receiptId = $this->_getParam('receipt_id'); 
        $this->_model->setReceiptId($receiptId);
        $this->view->receipt = $this->_model->fetch();
        $this->view->receiptId = $receiptId;	
    }
   
    /**
     * Export the invoice to PDF document
     */
    public function exportAction()
    {
        $receiptId = $this->_getParam('receipt_id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_model->setReceiptId($receiptId);
        $fileName = $this->_model->getPdfFileLocation();
        $file = file_get_contents($fileName);

        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="receipt.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file);
    }
        
}

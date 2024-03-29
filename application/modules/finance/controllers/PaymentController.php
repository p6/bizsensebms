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

class Finance_PaymentController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Finance_Payment
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    function init()
    {
        $this->_model = new Core_Model_Finance_Payment;
    }

    /**
     * Browsable, sortable, searchable list of Finance Payments
     */
    public function indexAction()
    {
       $form = new Core_Form_Finance_Payment_Search;
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
    
    public function sccashpaymentAction()
    {
       $form = new Core_Form_Finance_Payment_CashCreate;
        $form->setAction(
            $this->_helper->url(
                'sccashpayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createCashPayment($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                   'The cash payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Create a new Sundry Creditors Cheque payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function scchequepaymentAction()
    {
       $form = new Core_Form_Finance_Payment_ChequeCreate;
        $form->setAction(
            $this->_helper->url(
                'scchequepayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createChequePayment(
                                                $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                 'The cheque payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Create a new Expenses Cash payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function ecashpaymentAction()
    {
       $form = new Core_Form_Finance_Payment_ExpensesCashCreate;
        $form->setAction(
            $this->_helper->url(
                'ecashpayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createExpensesCashPayment(
                                                $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                         'The expenses cash Payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Create a new Expenses Cheque payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function echequepaymentAction()
    {
       $form = new Core_Form_Finance_Payment_ExpensesChequeCreate;
        $form->setAction(
            $this->_helper->url(
                'echequepayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createExpensesChequePayment(
                                                $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                       'The expenses cheque payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Cash Payment Details 
     */ 
    public function cashpaymentdetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;
    }
    
    /**
     * Cheque Payment Details 
     */ 
    public function chequepaymentdetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Edit a Cash Payment
     */
    public function editcashpaymentAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditCashPayment($this->_model);
        $form->setAction($this->_helper->url(
                'editcashpayment', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editCashPayment($form->getValues());
                $this->_helper->FlashMessenger(
                              'The cash payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        }
        else {
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Edit a Cheque Payment
     */
    public function editchequepaymentAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditChequePayment($this->_model);
        $form->setAction($this->_helper->url(
                'editchequepayment', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editChequePayment($form->getValues());
                $this->_helper->FlashMessenger(
                           'The cheque payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment_Bank;
            $paymentBankRecord = $paymentBankModel->fetchbyPaymentId(
                                        $paymentId);    
            $defaultValues['instrument_account_no'] = 
                                $paymentBankRecord['instrument_number'];
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['instrument_date']);
            $defaultValues['instrument_date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['instrument_date']);
            
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
        
    }
    
    /**
     * Edit a Expenses Cash Payment
     */
    public function editexpensescashpaymentAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = 
            new Core_Form_Finance_Payment_EditExpensesCashCreate($this->_model);
        $form->setAction($this->_helper->url(
                'editexpensescashpayment', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editExpensesCashPayment($form->getValues());
                $this->_helper->FlashMessenger(
                    'The expenses cash payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Edit a Expenses Cheque Payment
     */
    public function editexpenseschequepaymentAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditExpensesCheque($this->_model);
        $form->setAction($this->_helper->url(
                'editexpenseschequepayment', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editExpensesChequePayment($form->getValues());
                $this->_helper->FlashMessenger(
                                    'The Payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment_Bank;
            $paymentBankRecord = $paymentBankModel->fetchbyPaymentId(
                                        $paymentId);    
            $defaultValues['instrument_account_no'] = 
                                $paymentBankRecord['instrument_number'];
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['instrument_date']);
            $defaultValues['instrument_date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['instrument_date']);
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Delete the Payment
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setPaymentId($this->_getParam('payment_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The payment was successfully deleted'; 
        } else {
           $message = 'The payment could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'payment', 'finance');     
        
    }
    
    /**
     * Create a new TDS Cash payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function tdscashAction()
    {
       $form = new Core_Form_Finance_Payment_CreateTdsCash;
        $form->setAction(
            $this->_helper->url(
                'tdscash',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createTdsCash($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                              'The TDS cash payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Cash TDS Cash Payment Details 
     */ 
    public function tdscashdetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Edit a TDS Cash Payment
     */
    public function edittdscashAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditTdsCash($this->_model);
        $form->setAction($this->_helper->url(
                'edittdscash', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editTdsCash($form->getValues());
                $this->_helper->FlashMessenger(
                          'The TDS cash payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Create a new TDS Cheque payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function tdschequeAction()
    {
       $form = new Core_Form_Finance_Payment_TdsCheque;
        $form->setAction(
            $this->_helper->url(
                'tdscheque',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createTdsChequePayment(
                                               $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                            'The TDS cheque payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Cash TDS Cheque Payment Details 
     */ 
    public function tdschquedetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Edit a TDS Cheque Payment
     */
    public function edittdschequeAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditTdsCheque($this->_model);
        $form->setAction($this->_helper->url(
                'edittdscheque', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editTdsCheque($form->getValues());
                $this->_helper->FlashMessenger(
                          'The TDS cash payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment_Bank;
            $paymentBankRecord = $paymentBankModel->fetchbyPaymentId(
                                        $paymentId);    
            $defaultValues['instrument_account_no'] = 
                                $paymentBankRecord['instrument_number'];
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['instrument_date']);
            $defaultValues['instrument_date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['instrument_date']);
                                    
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }  
    
    /**
     * Create a new Tax Cache payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function taxcashAction()
    {
       $form = new Core_Form_Finance_Payment_CreateTaxCash;
        $form->setAction(
            $this->_helper->url(
                'taxcash',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createTaxCashPayment(
                                               $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                            'The Tax cash payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Create a new Tax Cheque payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function taxchequeAction()
    {
       $form = new Core_Form_Finance_Payment_TaxCheque;
        $form->setAction(
            $this->_helper->url(
                'taxcheque',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createTaxChequePayment(
                                               $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                            'The Tax cheque payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    
    /**
     * Cash Tax Cash Payment Details 
     */ 
    public function taxcashdetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Edit a Tax Cash Payment
     */
    public function edittaxcashAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditTaxCash($this->_model);
        $form->setAction($this->_helper->url(
                'edittaxcash', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editTaxCash($form->getValues());
                $this->_helper->FlashMessenger(
                          'The Tax cash payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Cash Tax Cheque Payment Details 
     */ 
    public function taxchequedetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Edit a Tax Cheque Payment
     */
    public function edittaxchequeAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditTaxCheque($this->_model);
        $form->setAction($this->_helper->url(
                'edittaxcheque', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editTaxCheque($form->getValues());
                $this->_helper->FlashMessenger(
                          'The Tax cheque payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment_Bank;
            $paymentBankRecord = $paymentBankModel->fetchbyPaymentId(
                                        $paymentId);    
            $defaultValues['instrument_account_no'] = 
                                $paymentBankRecord['instrument_number'];
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['instrument_date']);
            $defaultValues['instrument_date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['instrument_date']);
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
           
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }  
    
    /**
     * Create a new Salaray Cash payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function salarycashpaymentAction()
    {
       $form = new Core_Form_Finance_Payment_SalaryCashCreate;
        $form->setAction(
            $this->_helper->url(
                'salarycashpayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createSalaryCashPayment(
                                                $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                          'The Salaray cash payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Create a new Salaray Cash payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function salarychequepaymentAction()
    {
       $form = new Core_Form_Finance_Payment_SalaryChequeCreate;
       $form->setAction(
            $this->_helper->url(
                'salarychequepayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createSalaryChequePayment(
                                                $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                         'The Salaray cheque payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Cash Salary Cash Payment Details 
     */ 
    public function salarycashdetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Cash Salary Cheque Payment Details 
     */ 
    public function salarychequedetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Edit a Cash Payment
     */
    public function editsalarycashAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditSalaryCash($this->_model);
        $form->setAction($this->_helper->url(
                'editsalarycash', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editSalaryCash($form->getValues());
                $this->_helper->FlashMessenger(
                        'The salary cash payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Edit a Cash Payment
     */
    public function editsalarychequeAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditSalaryCheque($this->_model);
        $form->setAction($this->_helper->url(
                'editsalarycheque', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editSalaryCheque($form->getValues());
                $this->_helper->FlashMessenger(
                  'The salary cheque payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment_Bank;
            $paymentBankRecord = $paymentBankModel->fetchbyPaymentId(
                                        $paymentId);    
            $defaultValues['instrument_account_no'] = 
                                $paymentBankRecord['instrument_number'];
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['instrument_date']);
            $defaultValues['instrument_date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['instrument_date']);
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * select payment
     */
    public function selectpaymentAction()
    {
        $form = new Core_Form_Finance_Payment_SelectPayment;
        $form->setAction(
            $this->_helper->url(
                'selectpayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $action = $this->_model->selectPayment(
                                            $this->getRequest()->getPost());
                $this->_helper->redirector(
                    $action,'payment', 'finance', 
                    array('purchase_id'=>$purchaseId)
                );
            }
        }
    }
    
    /**
     * Create a new advance Cash payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function advancecashpaymentAction()
    {
       $form = new Core_Form_Finance_Payment_SalaryCashCreate;
        $form->setAction(
            $this->_helper->url(
                'advancecashpayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createAdvanceCashPayment(
                                                $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                       'The Salaray cash payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Create a new advance Cash payment 
     *
     * @see Zend_Controller_Action
     */ 
    public function advancechequepaymentAction()
    {
       $form = new Core_Form_Finance_Payment_SalaryChequeCreate;
       $form->setAction(
            $this->_helper->url(
                'advancechequepayment',
                'payment',
                'finance'
            )
        );

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->createAdvanceChequePayment(
                                                $this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                       'The Salaray cheque payment was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Cash advance Cash Payment Details 
     */ 
    public function advancecashdetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Cash advance Cheque Payment Details 
     */ 
    public function advancechequedetailsAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        $this->view->payment = $this->_model->fetch();
        $this->view->paymentId = $paymentId;	
    }
    
    /**
     * Edit a Cash Payment
     */
    public function editadvancecashAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditSalaryCash($this->_model);
        $form->setAction($this->_helper->url(
                'editadvancecash', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editAdvanceCash($form->getValues());
                $this->_helper->FlashMessenger(
                 'The advance cash payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
    /**
     * Edit a Cash Payment
     */
    public function editadvancechequeAction()
    {
        $paymentId = $this->_getParam('payment_id'); 
        $this->_model->setPaymentId($paymentId);
        
        $form = new Core_Form_Finance_Payment_EditSalaryCheque($this->_model);
        $form->setAction($this->_helper->url(
                'editadvancecheque', 
                'payment', 
                'finance',
                array(
                    'payment_id'=>$paymentId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->editAdvanceCheque($form->getValues());
                $this->_helper->FlashMessenger(
                    'The advance cheque payment has been edited successfully');
                $this->_helper->redirector('index', 'payment', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $paymentBankModel = new Core_Model_Finance_Payment_Bank;
            $paymentBankRecord = $paymentBankModel->fetchbyPaymentId(
                                        $paymentId);    
            $defaultValues['instrument_account_no'] = 
                                $paymentBankRecord['instrument_number'];
            $date = new Zend_Date();
            $date->setTimestamp($paymentBankRecord['instrument_date']);
            $defaultValues['instrument_date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['instrument_date']);
            $paymentBankModel = new Core_Model_Finance_Payment($paymentId);
            $paymentBankRecord = $paymentBankModel->fetch();    
            $date->setTimestamp($paymentBankRecord['date']);
            $defaultValues['date'] = $this->view->timestampToDojo(
                                    $paymentBankRecord['date']);
            $form->populate($defaultValues);
        }
    }
    
}

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
class Finance_BankaccountController extends Zend_Controller_Action 
{

    /**
     * @var object Core_Model_Finance_BankAccount
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    function init()
    {
        $this->_model = new Core_Model_Finance_BankAccount;
    }

    /**
     * Browsable, sortable, searchable list of Bankaccounts
     */
    public function indexAction()
    {
       $paginator = $this->_model->getPaginator();  
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
    }

    /**
     * Create a new Bank Account
     *
     * @see Zend_Controller_Action
     */  
    public function createAction()
    {
        $form = new Core_Form_Finance_BankAccount_Create;
        $form->setAction($this->_helper->url('create', 'bankaccount', 'finance'));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->create($this->getRequest()->getPost());
                $message = 'The Bank Account was created successfully';
                $this->_helper->FlashMessenger($message);
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Edit a Bank Account
     */
    public function editAction()
    {
        $bankaccountId = $this->_getParam('bank_account_id'); 
        $this->_model->setBankAccountId($bankaccountId);
        
        $form = new Core_Form_Finance_BankAccount_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'bankaccount', 
                'finance',
                array(
                    'bank_account_id'=>$bankaccountId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger(
                    'The Bank account has been edited successfully'
                );
                $this->_helper->redirector('index', 'bankaccount', 'finance',
                    array('bank_account_id' => $bankaccountId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }
    
    /**
     * Delete the bank account
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setBankAccountId($this->_getParam('bank_account_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The bank account was successfully deleted'; 
        } else {
           $message = 'The bank account could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'bankaccount', 'finance');     
        
    }
    
    public function jsonstoreAction()
    {
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('bank_account_id', $items);
        $this->_helper->AutoCompleteDojo($data);

    }
    
    /**
     * Deposit Cash process
     *
     * @see Zend_Controller_Action
     */ 
    public function depositcashAction()
    {
        $bankaccountId = $this->_getParam('bank_account_id'); 
        $this->_model->setBankAccountId($bankaccountId);
                
        $form = new Core_Form_Finance_BankAccount_DepositCash;
        $action = $this->_helper->url(
                'depositcash',
                'bankaccount',
                'finance',
                array(
                    'bank_account_id' => $bankaccountId
                )
        );
        
        $form->setAction($action);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->depositcash($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                   'The cash deposited successfully');
                $this->_helper->redirector(
                    'transaction', 
                    'bankaccount', 
                    'finance',
                    array(
                        'bank_account_id' => $bankaccountId,
                    )
                );
            }
        }
    }
    
    /**
     * Withdraw Cash process
     *
     * @see Zend_Controller_Action
     */ 
    public function withdrawcashAction()
    {
        $bankaccountId = $this->_getParam('bank_account_id'); 
        $this->_model->setBankAccountId($bankaccountId);
        
        $form = new Core_Form_Finance_BankAccount_WithdrawCash;
        $action = $this->_helper->url(
                'withdrawcash',
                'bankaccount',
                'finance',
                array(
                    'bank_account_id' => $bankaccountId
                )
        );
        
        $form->setAction($action);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
               $this->_model->withdrawcash($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                   'The cash withdrawn successfully');
                $this->_helper->redirector(
                    'transaction', 
                    'bankaccount', 
                    'finance',
                    array(
                        'bank_account_id' => $bankaccountId,
                    )
                );
            }
        }
    }
    
    /**
     *  reconciliation
     *
     * @see Zend_Controller_Action
     */ 
    public function reconciliationAction()
    {
        $this->view->bankaccount = $this->_getParam('bank_account_id'); 
    }
    
    /**
     * Confirm reconciliation
     *
     * @see Zend_Controller_Action
     */ 
    public function confirmAction()
    {
        $bankaccountId = $this->_getParam('bank_account_id'); 
        $receiptBankId = $this->_getParam('receipt_bank_id'); 
        $paymentBankId = $this->_getParam('payment_bank_id');
        
        $this->_model->setBankAccountId($bankaccountId);
        
        $form = new Core_Form_Finance_BankAccount_Reconciliation;
        $action = $this->_helper->url(
                'confirm',
                'bankaccount',
                'finance',
                array(
                    'bank_account_id' => $bankaccountId,
                    'receipt_bank_id' => $receiptBankId,
                    'payment_bank_id' => $paymentBankId,
                )
        );
        
        $form->setAction($action);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
               $this->_model->reconciliationConfirm(
                        $this->getRequest()->getPost(), 
                                            $receiptBankId, $paymentBankId);
                $this->_helper->FlashMessenger(
                           'The Reconciliation was completed successfully');
                $this->_helper->redirector(
                    'reconciliation', 
                    'bankaccount', 
                    'finance',
                    array(
                        'bank_account_id' => $bankaccountId,
                    )
                );
            }
        }
    }
    
    /**
     * Confirm reconciliation
     *
     * @see Zend_Controller_Action
     */ 
    public function returnedAction()
    {
        $bankaccountId = $this->_getParam('bank_account_id'); 
        $receiptBankId = $this->_getParam('receipt_bank_id'); 
        $paymentBankId = $this->_getParam('payment_bank_id');
        
        $this->_model->setBankAccountId($bankaccountId);
        
        $form = new Core_Form_Finance_BankAccount_Returned;
        $action = $this->_helper->url(
                'returned',
                'bankaccount',
                'finance',
                array(
                    'bank_account_id' => $bankaccountId,
                    'receipt_bank_id' => $receiptBankId,
                    'payment_bank_id' => $paymentBankId,
                )
        );
        
        $form->setAction($action);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
               $this->_model->returned(
                        $this->getRequest()->getPost(), 
                        $bankaccountId,$receiptBankId, $paymentBankId);
                $this->_helper->FlashMessenger(
                      'The Cheque Returned process completed successfully');
                $this->_helper->redirector(
                    'reconciliation', 
                    'bankaccount', 
                    'finance',
                    array(
                        'bank_account_id' => $bankaccountId,
                    )
                );
            }
        }
    
    }
    
    /**
     * bank transaction
     */ 
    public function transactionAction()
    {
        $this->view->bankaccountId = $this->_getParam('bank_account_id');
        $search['bank_account_id'] = $this->view->bankaccountId;
        $bankTransactionModel = new Core_Model_Finance_BankAccount_Transaction;
        $paginator = $bankTransactionModel->getPaginator($search);  
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }
    
    /**
     * edit bank transaction
     */ 
    public function edittransactionAction()
    {
        $bankTrasactionId = $this->_getParam('bank_transaction_id'); 
        $bankaccountId = $this->_getParam('bank_account_id');
        $bankTransactionModel = 
                                new Core_Model_Finance_BankAccount_Transaction;
        $bankTransactionModel->setBankTransactionId($bankTrasactionId);
            
        $form = new Core_Form_Finance_BankAccount_TransactionEdit();
        $form->setAction($this->_helper->url(
                'edittransaction', 
                'bankaccount', 
                'finance',
                array(
                    'bank_account_id'=>$bankaccountId,
                    'bank_transaction_id' => $bankTrasactionId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $bankTransactionModel->editTransaction($form->getValues());
                $this->_helper->FlashMessenger(
                    'The bank account transaction has been edited successfully'
                );
                $this->_helper->redirector('transaction','bankaccount','finance',
                    array('bank_account_id' => $bankaccountId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $bankTransactionRecord = $bankTransactionModel->fetch();
            $bankTransactionRecord['cashaccount_id'] = 
                                      $bankTransactionRecord['cash_account_id'];
            $form->populate($bankTransactionRecord);
            
        }
    }
    
    /**
     * delete bank transaction
     */ 
    public function deletetransactionAction()
    {       
        $bankaccountId = $this->_getParam('bank_account_id');
        $bankTrasactionId = $this->_getParam('bank_transaction_id');
        $bankTransactionModel = new Core_Model_Finance_BankAccount_Transaction;
        
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $bankTransactionModel
                        ->setBankTransactionId($bankTrasactionId)
                        ->delete();

        if ($deleted) {
           $message = 'The bank transaction was successfully deleted'; 
        } else {
           $message = 'The bank transaction could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $url = $this->_helper->url(
                        'transaction',
                        'bankaccount',
                         'finance',
                        array(
                        'bank_account_id'=>$bankaccountId,
                        'bank_transaction_id' => $bankTrasactionId
                       )
                    );
        $this->_redirect($url);
    }
    
 
} 

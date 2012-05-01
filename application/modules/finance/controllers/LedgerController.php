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

class Finance_LedgerController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Finance_Ledger
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    function init()
    {
        $this->_model = new Core_Model_Finance_Ledger;
    }	

    /**
     * Browsable, sortable, searchable list of Finance Ledgers
     */
    public function indexAction() 
    {
        $paginator = $this->_model->getPaginator('', $this->_getParam('sort'));  
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    /**
     * Create a new Ledger
     *
     * @see Zend_Controller_Action
     */ 
    public function createAction()
    {
        $form = new Core_Form_Finance_Ledger_Create;
        $form->setAction($this->_helper->url('create', 'ledger', 'finance'));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->create($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                         'The ledger was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Create a new Ledger Entry
     *
     * @see Zend_Controller_Action
     */ 
    public function entriesAction()
    {
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
        $faLedgerId = $this->_getParam('fa_ledger_id');
        $ledgerEntryModel->setLedgerId($faLedgerId);
        
        $form = new Core_Form_Finance_Ledger_Entry;
        $form->populate($_GET);
        
        $form->setAction($this->_helper->url(
                'entries', 
                'ledger', 
                'finance',
                array(
                    'fa_ledger_id'=>$faLedgerId
                )
            )
        );
        $this->view->form = $form;
        $this->view->items = $form->getValues();
        
        if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
         
        $paginator = $ledgerEntryModel->getPaginator($form->getValues(), 
                                                    $this->_getParam('sort'));  
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
       
        $this->view->ledgerRecord = 
                               $this->_model->setLedgerId($faLedgerId)->fetch();
    }
    
    /**
     * Stores list of Ledger Ids of Indirect Expenses for DOJO Dropdown button
     */
    public function jsonstoreAction()
    {
        $items = (array) $this->_model->fetchByGroup('Indirect Expenses');
        $data = new Zend_Dojo_Data('fa_ledger_id', $items);
        $this->_helper->AutoCompleteDojo($data);

    }
    
    /**
     * Stores list of Ledger Ids of Indirect Incomes for DOJO Dropdown button
     */
    public function incomejsonstoreAction()
    {
        $items = (array) $this->_model->fetchByGroup('Indirect Incomes');
        $data = new Zend_Dojo_Data('fa_ledger_id', $items);
        $this->_helper->AutoCompleteDojo($data);

    }
    
    /**
     * Stores list of Ledger Ids of Duties And Taxes for DOJO Dropdown button
     */
    public function tdsstoreAction()
    {
        $items = (array) $this->_model->fetchByGroup('Duties And Taxes');
        $data = new Zend_Dojo_Data('fa_ledger_id', $items);
        $this->_helper->AutoCompleteDojo($data);

    }
    
    /**
     * Stores list of Ledger Ids of Duties And Taxes for DOJO Dropdown button
     */
    public function salarystoreAction()
    {
        $items = (array) $this->_model->fetchByGroup('Salaries Payable');
        $data = new Zend_Dojo_Data('fa_ledger_id', $items);
        $this->_helper->AutoCompleteDojo($data);

    }
    
    /**
     * Stores list of Ledger Ids of Duties And Taxes for DOJO Dropdown button
     */
    public function ledgerstoreAction()
    {
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('fa_ledger_id', $items);
        $this->_helper->AutoCompleteDojo($data);

    }
    
    public function csvexportAction()
    {
        $data['from'] = $this->_getParam('from');
        $data['to'] = $this->_getParam('to');
        $data['notes'] = $this->_getParam('notes');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
               
        $ledgerEntryId = $this->_getParam('fa_ledger_id');
        $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry();
        $ledgerEntryModel->setLedgerEntryId($ledgerEntryId);
        
        $file = $ledgerEntryModel->csvExport($data);
             
        $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 'attachment; filename=ledger.csv')
                            ->appendBody($file);
    }
    
    /**
     * @return fluent interface
     */
    public function editopeningbalanceAction()
    {
       $faLedgerId = $this->_getParam('fa_ledger_id');
       $this->_model->setLedgerId($faLedgerId);
        
       $form = new Core_Form_Finance_Ledger_InitializeLedger;
        $form->setAction($this->_helper->url(
                'editopeningbalance', 
                'ledger', 
                'finance',
                 array(
                    'fa_ledger_id'=>$faLedgerId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {                               
                $this->_model->editOpeningBalance($form->getValues());
                $this->_helper->FlashMessenger(
                      'The Opening Balance has been Updated successfully');
                $this->_helper->redirector('index', 'ledger', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $ledgerModel = new Core_Model_Finance_Ledger($faLedgerId);
           
            $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry();
            $ledgerEntryId = $ledgerModel->getOpeningBalanceId();
            $ledgerEntryModel->setLedgerEntryId($ledgerEntryId);
            $ledgerEntryRecord = $ledgerEntryModel->fetch();
            
            if ($ledgerEntryRecord['debit'] != 0.0) {
                $defaultValues['opening_balance'] = $ledgerEntryRecord['debit'];
                $defaultValues['opening_balance_type'] = 
                         Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT;
                $form->populate($defaultValues);
            }
            
            if ($ledgerEntryRecord['credit'] != 0.0) {
                $defaultValues['opening_balance'] = $ledgerEntryRecord['credit'];
                $defaultValues['opening_balance_type'] = 
                        Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_CREDIT;
                $form->populate($defaultValues);
            }
        }

    }
    
    /**
     * Delete the ledger
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setLedgerId($this->_getParam('fa_ledger_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The ledger was successfully deleted'; 
        } else {
           $message = 'The ledger could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'ledger', 'finance');  
        
    }
    
    /**
     * Delete the ledger
     */
    public function createledgerentryAction()
    {
        $ledgerId = $this->_getParam('fa_ledger_id');
        $form = new Core_Form_Finance_Ledger_CreateEntry;
        $action = $this->_helper->url(
                'createledgerentry',
                'ledger',
                'finance',
                array(
                    'fa_ledger_id' => $ledgerId
                )
        );
        $form->setAction($action);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $ledgerEntryModel = new Core_Model_Finance_Ledger_Entry;
                $data = $this->getRequest()->getPost();
                $dataToInsert['fa_ledger_id'] = $ledgerId;
                if ($data['balance_type'] == 1) {
                    $dataToInsert['debit'] = $data['balance'];
                }
                else {
                    $dataToInsert['credit'] = $data['balance'];
                }
                $dataToInsert['notes'] = $data['notes'];
                $dataToInsert['transaction_timestamp'] = time();
                $ledgerEntryModel->create($dataToInsert);
                
                $this->_helper->FlashMessenger(
                                   'The ledger entry was created successfully');
                $url = $this->_helper->url(
                        'entries',
                        'ledger',
                        'finance',
                        array(
                         'fa_ledger_id' => $ledgerId
                        )
                    );
                $this->_redirect($url);
            }
        }
    }
    
    /**
     * closing all accounts
     */ 
    public function closeaccountsAction()
    {
        $form = new Core_Form_Finance_Ledger_CloseAccounts;
        $form->setAction($this->_helper->url(
                                        'closeaccounts', 'ledger', 'finance'));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->closeAccounts($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                             'All financial accounts are closed successfully');
                $url = $this->_helper->url(
                        'index',
                        'index',
                        'finance',
                        array(
                         'fa_ledger_id' => $ledgerId
                        )
                    );
                $this->_redirect($url);
            }
        }
    }
} 

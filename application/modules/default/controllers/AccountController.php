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
 * an electronic mail 
 * to info@binaryvibes.co.in
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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. 
 * (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class AccountController extends Zend_Controller_Action 
{
    public $db;
    protected $_model;

    public function init()
    {
        $this->db = Zend_Registry::get('db');
        $this->_model = new Core_Model_Account;
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('viewdetails', 'json')
                    ->initContext();
    }

    /**
     * Browsable, searchable list of accounts
     */
    public function indexAction() 
    {
        $sForm = new Core_Form_Account_Search;
        $form = $sForm->getForm();
        $form->populate($_GET);
        $this->view->form = $form;

        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;

        /*
         * Notify the view if the submit was hit on the search form
         */
        if ($form->getValue('submit') == 'Search') {
            $this->view->wasSearched = true;
        }
    } 

    /**
     * Browsable, sortable, searchable list of account notes
     */
    public function notesAction()
    {
        $account_id = $this->_getParam('account_id');
        $this->view->account_id = $account_id;

        $notes = new Core_Model_Account_Notes();
        $notes->setAccountId($account_id);
        $paginator = $notes->getPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;

    }

    /**
     * Create a note to this account
     */
    public function createnoteAction()
    {
        $accountId = $this->_getParam('account_id');
        $this->view->account_id = $accountId;
        $this->_model->setAccountId($accountId);
        $notes = $this->_model->getNotes();
        $form = new Core_Form_Account_Note_Create();
        $form->setAction($this->_helper->url(
                'createnote',
                'account',
                'default',
                array(
                    'account_id' => $accountId,
                )
            )
        );

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $accountNotesId = $notes->create($form->getValues());
                $this->_helper->FlashMessenger('Account note was created successfully');
                $this->_helper->Redirector('notes', 'account', 'default', array('account_id'=>$accountId));
		        $this->view->message = "Account was created successfully";
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

         } else {
            $this->view->form = $form;
	     }

    }

    /**
     * View details of the account
     */
    public function viewdetailsAction() 
    {
	    $accountId = $this->_request->getParam('account_id');
        $account = $this->_model->setId($accountId);    
	    $this->view->account = $account->fetch();
		
    } 

    /**
     * Create account
     */
    public function createAction() 
    {
        $db = $this->db;
        $account = new Core_Model_Account;
	
        $form = new Core_Form_Account_Create;
        $form->setAction($this->_helper->url('create', 'account', 'default'));

        if ($this->_request->isPost()) {
        
            if ($form->isValid($_POST)) {
                $accountId = $account->create($form->getValues());
                $this->_helper->FlashMessenger('Account was created successfully');
                $url = $this->view->url(array(
                    'module'        =>  'default',
                    'controller'    =>  'account',
                    'action'        =>  'viewdetails',
                    'account_id'    =>  $accountId,        
                ), null, true);
	 	        $this->_redirect($url);
		        $this->view->message = "Account was created successfully";
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

         } else {
            $this->view->form = $form;
	     }

    } 


    /**
     * Edit an account
     */
    public function editAction() 
    {
        $db = $this->db;

	    $accountId = $this->_getParam('account_id');	
        $account = new Core_Model_Account($accountId);

        $form = new Core_Form_Account_Edit($accountId);

        if ($this->_request->isPost()){
            if ($form->isValid($_POST)){
                $account->edit($form->getValues());
		        $this->_helper->FlashMessenger('Account edited successfully');
	 	        $this->_redirect($this->view->url(array(
                    'module'        =>  'default',
                    'controller'    =>  'account',
                    'action'        =>  'viewdetails',
                    'account_id'    =>  $accountId,    
                )));
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
	    }

	
    } 

    /*
     * Delete an account
     */
    public function deleteAction()
    {     
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setAccountId($this->_getParam('account_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The account was successfully deleted'; 
        } else {
           $message = 'The account could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'account', 'default');       
        
    }

    /*
     * Assign lead to a branch while creating and editing a lead
     * @output Zend_Dojo_Data for the autocomplete field
     */
    public function assigntobranchAction()
    {

        $data = $this->_model->getAssignedToBranchDojoData();
        $this->_helper->autoCompleteDojo($data);
    }

    /*
     * CODE REVIEW: MARKED FOR DELETION
     */
    public function jsonstoreAction()
    {
        $this->_helper->layout->disableLayout();
              
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('account_id', $items);
        $this->_helper->AutoCompleteDojo($data);

   }
	
    /**
     * List the contacts of the account
     */ 
    public function contactsAction()
    {
        $accountId = $this->_getParam('account_id');
        $this->_model->setId($accountId);
        $contacts = $this->_model->getContacts();
        $this->view->contacts = $contacts;
        
        $this->view->account = $this->_model->fetch();
    }
 
    public function initializeledgerAction()
    {
        $accountId = $this->_getParam('account_id'); 
        $this->_model->setAccountId($accountId);
  
        $form = new Core_Form_Finance_Ledger_InitializeLedger;
        $form->setAction($this->_helper->url(
                'initializeledger', 
                'account', 
                'default',
                array(
                    'account_id'=>$accountId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->initializeLedger($form->getValues());
                $this->_helper->FlashMessenger('The Ledger Initialized successfully');
                $this->_helper->redirector('viewdetails', 'account', 'default',
                    array('account_id'=>$accountId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }

    public function importAction()
    {
        $form = new Core_Form_Account_Import;
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->account_import_csv->receive()) {
                    $location = $form->account_import_csv->getFileName();
                }
            $noOfAffectedRows = $this->_model->import($form->getValues(), $location);
            if($noOfAffectedRows >= 1 ) {
                $this->_helper->FlashMessenger("
                $noOfAffectedRows accounts imported successfully");
            }
            else {
                $this->_helper->FlashMessenger("Error in importing accounts");
            }
            $this->_helper->redirector('index', 'account', 'default');
            }else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
        }
    }

} 

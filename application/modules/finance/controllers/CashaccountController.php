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

class Finance_CashaccountController extends Zend_Controller_Action 
{

    /**
     * @var object Core_Model_Finance_CashAccount
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    function init()
    {
        $this->_model = new Core_Model_Finance_CashAccount;
    }

    /**
     * Browsable, sortable, searchable list of Cash Accounts
     */
    public function indexAction()
    {
       $form = new Core_Form_Finance_CashAccount_Search;
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
     * Create a new Cash Account
     *
     * @see Zend_Controller_Action
     */ 
    public function createAction()
    {
        $form = new Core_Form_Finance_CashAccount_Create;
        $form->setAction($this->_helper->url('create', 'cashaccount', 'finance'));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->create($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                   'The Cash Account was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Edit a Cash Account
     */
    public function editAction()
    {
        $cashaccountId = $this->_getParam('cash_account_id'); 
        $this->_model->setCashaccountId($cashaccountId);
        
        $form = new Core_Form_Finance_CashAccount_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'cashaccount', 
                'finance',
                array(
                    'cash_account_id'=>$cashaccountId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger(
                               'The Cash account has been edited successfully');
                $this->_helper->redirector('index', 'Cashaccount', 'finance',
                    array('campaign_id'=>$campaignId));
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
                        ->setCashAccountId($this->_getParam('cash_account_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The cash account was successfully deleted'; 
        } else {
           $message = 'The cash account could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'cashaccount', 'finance');    
        
    }
 
    /**
     * Stores list of Cash account Id for DOJO Dropdown button
     */
    public function jsonstoreAction()
    {
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('cash_account_id', $items);
        $this->_helper->AutoCompleteDojo($data);

    }
} 

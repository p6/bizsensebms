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

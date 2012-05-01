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

interface
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

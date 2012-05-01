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

class NewsLetter_QueueController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Variable
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_Newsletter_Message_Queue; 
    }

    /**
     * mail queue settings
     */
    public function settingsAction()
    {
        $form = new Core_Form_Newsletter_Queue_Settings;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {    
                $this->_model->saveSettings($form->getValues());
                $this->_helper
                        ->FlashMessenger(
                            'Message queue settings saved successfully'
                        ); 
                $this->_helper->redirector('index', 'settings', 'newsletter');
            } else {
                $form->populate($_POST);
                $this->view->form = $form;                
            }
        } else {
            $dataToPopulate = $this->_model->fetchSettings();
            $form->populate($dataToPopulate);
            $this->view->form = $form;
        }
    }

    /**
     * display message status
     */
    public function statusAction()
    {
        $this->_helper->layout->setLayout('layout_reports');
        $totalMessages = $this->_model->fetchTotalToBeSent();
        $this->view->totalMessages = $totalMessages;
        $totalMessagesWithHtmlFormat = $this->_model->fetchByHtmlFormat();
        $this->view->totalMessagesWithHtmlFormat = $totalMessagesWithHtmlFormat;
        $totalMessagesWithTextFormat = $this->_model->fetchByTextFormat();
        $this->view->totalMessagesWithTextFormat = $totalMessagesWithTextFormat;
        $totalUniqueDomains = $this->_model->fetchNumberOfUniqueDomains();
        $this->view->totalUniqueDomains = $totalUniqueDomains;

    }

    /**
     * remove all unsent message
     */
    public function cancelallAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_model->cancelAllPendingMessages(); 
        $this->_helper->FlashMessenger("Successfully cancelled all pending messages from queue");
        $this->_helper->redirector('status', 'queue', 'newsletter');
    }
    
    /**
     * remove message for unsent message
     */
    public function cancelAction()
    {
        $messageId = $this->_getParam('message_id'); 
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_model->cancelMessage($messageId);
        $this->_helper->FlashMessenger("Successfully cancelled this message from queue");
        $this->_helper->redirector('index', 'message', 'newsletter');
    }
    
    /**
     * display queue
     */
    public function indexAction()
    {
        $form = new Core_Form_Newsletter_Queue_Search;
        $form->populate($_GET);
        $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
        
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }


} 

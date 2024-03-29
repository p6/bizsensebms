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

class NewsLetter_MessageController extends Zend_Controller_Action 
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Newsletter_Message();
    }

    public function indexAction()
    {
        $paginator = $this->_model->getPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    public function createAction()
    {
        $form = new Core_Form_Newsletter_Message_Create;
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $messageId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('Message created successfully');
                $this->_helper->Redirector(
                    'edit', 'message', 'newsletter', 
                    array('message_id' => $messageId)
                );
            }
        }
    }

    public function editAction()
    {
        $messageId = $this->_getParam('message_id');
        $this->view->messageId = $messageId;
        $this->_model->setMessageId($messageId);
        $form = new Core_Form_Newsletter_Message_Create();
        $this->view->form = $form;
        $this->view->testMessageForm = 
            new Core_Form_Newsletter_Message_TestMessage();
        $testMessageFormAction = $this->_helper->url(
                'sendtestmessage', 'message', 'newsletter', 
                array('message_id'=>$messageId)
        );
        $this->view->testMessageForm->setAction($testMessageFormAction);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('Message created successfully');
                $this->_helper->Redirector(
                    'edit', 'message', 'newsletter', 
                    array('message_id' => $messageId)
                );
            }
        } else {
            $form->populate($this->_model->fetch());
        }
        
    }

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setMessageId($this->_getParam('message_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The message was successfully deleted'; 
        } else {
           $message = 'The message could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'message', 'newsletter');
    }

    public function textAction()
    {
        $messageId = $this->_getParam('message_id');
        $this->view->messageId = $messageId;
        $this->_model->setMessageId($messageId);
        $form = new Core_Form_Newsletter_Message_CreateText;
        $this->view->form = $form;
        
        $this->view->testMessageForm = 
            new Core_Form_Newsletter_Message_TestMessage();
        $testMessageFormAction = $this->_helper->url(
                'sendtestmessage', 'message', 'newsletter', 
                array('message_id'=>$messageId)
        );
        $this->view->testMessageForm->setAction($testMessageFormAction);
        
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('Message saved successfully');
                $this->_helper->Redirector(
                    'text', 'message', 'newsletter', 
                    array('message_id' => $messageId)
                );
            }
        } else {
            $form->populate($this->_model->fetch());
        }
    }

    public function sendtolistsAction()
    {
        $messageId = $this->_getParam('message_id');
        $this->view->messageId = $messageId;
        $this->_model->setMessageId($messageId);
        $form = new Core_Form_Newsletter_Message_AddToQueue();
        $form->setAction(
            $this->_helper->url(
                'sendtolists', 
                'message', 
                'newsletter', 
                array('message_id' => $messageId))
        );
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $result = $this->_model->sendToLists($form->getValue('lists'));
                $listsAlreadySend = array_unique($result);
                $message = "This message is already send to list ";
                if ($listsAlreadySend) {
                    for($i = 0; $i <= sizeof($listsAlreadySend)-1; $i += 1) {
                        $listModel = new Core_Model_Newsletter_List;
                        $listModel->setListId($result[$i]);
                        $listRecord = $listModel->fetch();
                        $listName = $listRecord['name'];
                        $message .= " '$listName' ,";
                    }
                    $message = rtrim($message, ",");
                    $this->_helper->flashMessenger($message);
                }
                else {
                    $this->_helper->flashMessenger(
                        'The message has been added to the queue'
                    );
                }
                $this->_helper->redirector('index', 'message', 'newsletter');
            }
        }
    }

    public function sendtestmessageAction()
    {
        $messageId = $this->_getParam('message_id');
        $recipient = $this->_getParam('recipient');
        $firstName = $this->_getParam('first_name');
        $middleName = $this->_getParam('middle_name');
        $lastName = $this->_getParam('last_name');

        $success = $this->_model
                    ->setMessageId($messageId)
                    ->sendTestMessage(
                        $recipient, $firstName, 
                        $middleName, $lastName
                    );
        if ($success !== true) {
            $this->getResponse()->setHttpResponseCode(403);
            $this->_helper->json(array('was sent'=> false));    
        } else {
            $this->_helper->json(array('was sent'=> true));    
        }
    }
    
    public function queuegraphAction()
    {
        $messageQueueModel = new Core_Model_Newsletter_Message_Queue;
        $this->view->messageUnsent = $messageQueueModel->fetchTotalToBeSent();
        $this->view->messageSent = $messageQueueModel->fetchTotalSent();
    }
}

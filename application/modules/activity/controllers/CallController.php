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

class Activity_CallController extends Zend_Controller_Action
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Activity_Call;
    }

    /*
     * List all the call entries
     */
    public function indexAction()
    {
        $form = new Core_Form_Activity_Call_Search;
        $form->populate($_POST);
        $this->view->form = $form;
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    /*
     * Create a call item 
     */
    public function createAction()
    {
        $form = new Core_Form_Activity_Call_Create;
        $form->setAction($this->_helper->url('create', 'call', 'activity'));
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $callId = $this->_model->create($form->getValues());
                $url = $this->view->url(array(
                    'module'        => 'activity',
                    'controller'    => 'call',
                    'action'        => 'viewdetails',
                    'call_id'       => $callId
                ));
                $this->_helper->FlashMessenger('The call details has been created successfully');
                $this->_redirect($url);
            }
        }
    }
   
    /**
     * Edit a call entry
     */ 
    public function editAction()
    {
        $callId = $this->_getParam('call_id');
        $this->_model->setCallId($callId);

        $form = new Core_Form_Activity_Call_Edit($this->_model);
        $form->setAction($this->_helper->url('edit', 'call', 'activity', array('call_id'=>$callId)));
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The call details has been edited successfully');
                $this->_helper->Redirector('index');
            } else {
               $form->populate($_POST);
            }
        } 
    }
 
    /**
     * View call details
     */
    public function viewdetailsAction()
    {
        $this->view->call = $this->_model->setCallId($this->_getParam('call_id'))->fetch();
    }

    /**
     * Delete a call entry
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setCallId($this->_getParam('call_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The call was successfully deleted'; 
        } else {
           $message = 'The call could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'call', 'activity');
    }

    public function notesAction()
    {
        $callId = $this->_getParam('call_id');
        $this->_model->setCallId($callId);
        $this->view->callId = $callId;
        $notes =  $this->_model->getNotes();
        $paginator = $notes->getPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    public function createnotesAction()
    {
        $callId = $this->_getParam('call_id');
        $this->_model->setCallId($callId);
        $model = $this->_model->getNotes();
        $form = new Core_Form_Activity_Note_Create;
        $form->setAction($this->view->url(array(
                'module' => 'activity',
                'controller' => 'call',
                'action' => 'createnotes',
                'call_id' => $callId,
            ), NULL, TRUE));

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $model->create($form->getValues());
                $this->_helper->FlashMessenger('Call note has been successfully created');
                $url = $this->view->url(array(
                    'module' => 'activity',
                    'controller' => 'call',
                    'action' => 'notes',
                    'call_id' => $callId,
                ), null, true);
                $this->_redirect($url);
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }
         } else {
            $this->view->form = $form;
        }
    }
}



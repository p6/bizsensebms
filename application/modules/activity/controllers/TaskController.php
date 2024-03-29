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

class Activity_TaskController extends Zend_Controller_Action
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Activity_Task;
    }

    /*
     * List all the task entries
     */
    public function indexAction()
    {
        $form = new Core_Form_Activity_Task_Search;
        $form->setMethod('get');
        $form->populate($_GET);
        $this->view->form = $form;
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    /*
     * Create a task item 
     */
    public function createAction()
    {
        $form = new Core_Form_Activity_Task_Create;
        $form->setAction($this->_helper->url('create', 'task', 'activity'));
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $taskId = $this->_model->create($form->getValues());
                $url = $this->view->url(array(
                    'module'        => 'activity',
                    'controller'    => 'task',
                    'action'        => 'viewdetails',
                    'task_id'       => $taskId
                ));
                $this->_helper->FlashMessenger('The task has been created successfully');
                $this->_redirect($url);
            }
        }
    }
   
    /**
     * Edit a task item
     */ 
    public function editAction()
    {
        $taskId = $this->_getParam('task_id');
        $this->_model->setTaskId($taskId);

        $form = new Core_Form_Activity_Task_Edit($this->_model);
        $form->setAction($this->_helper->url('edit', 'task', 'activity', array('task_id'=>$taskId)));
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The task has been edited successfully');
                $this->_helper->Redirector('index');
            } else {
               $form->populate($_POST);
            }
        } 
    }
 
    /**
     * View task item details
     */
    public function viewdetailsAction()
    {
        $this->view->task = $this->_model->setTaskId($this->_getParam('task_id'))->fetch();
        $notesPaginator = $this->_model->getNotes()->getPaginator();
        $notesPaginator->setCurrentPageNumber(0);
        $notesPaginator->setItemCountPerPage(10);
        
        $this->view->notesPaginator = $notesPaginator;
    }

    /**
     * Delete task item
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setTaskId($this->_getParam('task_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The task was successfully deleted'; 
        } else {
           $message = 'The task could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'task', 'activity');
    }

    public function notesAction()
    {
        $taskId = $this->_getParam('task_id');
        $this->_model->setTaskId($taskId);
        $this->view->taskId = $taskId;
        $notes =  $this->_model->getNotes();
        $paginator = $notes->getPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    public function createnotesAction()
    {
        $taskId = $this->_getParam('task_id');
        $this->_model->setTaskId($taskId);
        $model = $this->_model->getNotes();
        $form = new Core_Form_Activity_Note_Create;
        $form->setAction($this->view->url(array(
                'module' => 'activity',
                'controller' => 'task',
                'action' => 'createnotes',
                'task_id' => $taskId,
            ), NULL, TRUE));

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $model->create($form->getValues());
                $this->_helper->FlashMessenger('Task note has been successfully created');
                $url = $this->view->url(array(
                    'module' => 'activity',
                    'controller' => 'task',
                    'action' => 'notes',
                    'task_id' => $taskId,
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

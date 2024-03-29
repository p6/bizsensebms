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

class Ticket_IndexController extends Zend_Controller_Action
{
    /**
     * @var object Core_Model_Ticket
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_Ticket();
    }

    /**
     * Browsable, sortable, searchable list of tickets
     */
    public function indexAction()
    {
        $paginator = $this->_model->getPaginator();  
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;

    }

    /**
     * Create a ticket
     */
    public function createAction()
    {
        $form = new Core_Form_Ticket_Create();
        $form->setAction($this->_helper->url('create', 'index', 'ticket'));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $ticketId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('The ticket has been created successfully');
                $this->_helper->redirector('viewdetails', 'index', 'ticket', 
                    array('ticket_id'=>$ticketId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        }
    }

    /**
     * View the ticket details and comments
     * Add a comment to the ticket
     */
    public function viewdetailsAction()
    {
        $ticketId = $this->_getParam('ticket_id');
        $this->_model->setTicketId($ticketId);
    
        $form = new Core_Form_Ticket_Comment_Create();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $comment = $this->_model->getComment();
                $comment->create($form->getValues());
                $this->_helper->FlashMessenger('The comment was added successfully');
            } else {
                $form->populate($_POST);
            }
        }
        $this->view->ticket = $this->_model->fetch();
        $this->view->ticketComments = $this->_model->getComment()->fetchAll();

    }

    /**
     * Edit a ticket
     */
    public function editAction()
    {
        $ticketId = $this->_getParam('ticket_id');
        $this->_model->setTicketId($ticketId);

        $form = new Core_Form_Ticket_Create();
        $form->setAction(
            $this->_helper->url(
                'edit', 'index', 'ticket', 
                array('ticket_id'=>$ticketId)
            )
        );

        $this->view->form = $form;
    
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The ticket has been edited successfully');
                $this->_helper->redirector('viewdetails', 'index', 'ticket', 
                    array('ticket_id'=>$ticketId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } else {
            $form->populate($this->_model->fetch());
        }

    }

    /**
     * Delete a ticket
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setTicketId($this->_getParam('ticket_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The ticket was successfully deleted'; 
        } else {
           $message = 'The ticket could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'index', 'ticket');
   
    }

}

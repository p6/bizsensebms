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
 * You can contact Binary Vibes Information Technologies Pvt. 
 * Ltd. by sending an electronic mail to info@binaryvibes.co.in
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

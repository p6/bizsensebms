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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 */

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Ticket_StatusController extends Zend_Controller_Action
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Ticket_Status();
    }

    public function indexAction()
    {
        $paginator = $this->_model->getPaginator($this->_getParam('search'), $this->_getParam('sort'));  
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;

    }

    public function createAction()
    {
        $form = new Core_Form_Ticket_Status_Create();
        $form->setAction($this->_helper->url('create', 'status', 'ticket'));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $statusId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('The ticket status has been created successfully');
                $this->_helper->redirector('index', 'status', 'ticket', 
                    array('status_id'=>$statusId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        }
    }

    public function editAction()
    {
        
        $ticketStatusId = $this->_getParam('ticket_status_id');
        $this->_model->setTicketStatusId($ticketStatusId);

        $form = new Core_Form_Ticket_Status_Create();
        $form->setAction(
            $this->_helper->url(
                'edit', 'status', 'ticket', 
                array('ticket_status_id'=>$ticketStatusId)
            )
        );

        $this->view->form = $form;
    
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The ticket status has been edited successfully');
                $this->_helper->redirector('index', 'status', 'ticket', 
                    array('ticket_status_id'=>$ticketStatusId));
            } else {
                $form->populate($this->getRequest()->getPost());
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
                        ->setTicketStatusId($this->_getParam('ticket_status_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The ticket status was successfully deleted'; 
        } else {
           $message = 'The ticket status could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'status', 'ticket');
    }

}

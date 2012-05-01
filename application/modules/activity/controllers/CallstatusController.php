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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/** Add, modify, remove, list calls
 * 
 */

class Activity_CallstatusController extends Zend_Controller_Action
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Activity_Call_Status;
    }

    /*
     * List all the call status entries
     */
    public function indexAction()
    {
        $paginator = $this->_model->getPaginator(null, $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;
    }

    /*
     * Create a call status item 
     */
    public function createAction()
    {
        $form = new Core_Form_Activity_CallStatus_Create;
        $form->setAction($this->_helper->url('create', 'callstatus', 'activity'));
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->create($this->getRequest()->getPost());
                $this->_helper->FlashMessenger('The call status has been created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
   
    /**
     * Edit a call status item
     */ 
    public function editAction()
    {
        $callStatusId = $this->_getParam('call_status_id');
        $this->_model->setCallStatusId($callStatusId);
        $form = new Core_Form_Activity_CallStatus_Edit($this->_model);

        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($this->getRequest()->getPost());
                $this->_helper->FlashMessenger('The call status has been edited successfully');
                $this->_helper->Redirector('index');
            } else {
               $form->populate($_POST);
               $this->view->form = $form;
            }
        } else {
           $form->populate((array) $this->_model->setCallStatusId($this->_getParam('call_status_id'))->fetch());
           $this->view->form = $form;
        }
    }
 
    /**
     * View call status item details
     */

    public function viewdetailsAction()
    {
        $this->view->callStatus = $this->_model->setCallStatusId($this->_getParam('call_status_id'))->fetch();
    }

    /**
     * Delete call status
     */

    public function deleteAction()
    {        
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setCallStatusId($this->_getParam('call_status_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The call status was successfully deleted'; 
        } else {
           $message = 'The call status could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
         $this->_helper->Redirector('index', 'callstatus', 'activity');
    }
}


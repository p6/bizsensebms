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

/** Add, modify, remove, list tasks
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



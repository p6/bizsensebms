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

class Activity_MeetingController extends Zend_Controller_Action
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Activity_Meeting;
    }

    /*
     * List all the meeting entries
     */
    public function indexAction()
    {
        $form = new Core_Form_Activity_Meeting_Search;
        $form->populate($_POST);
        $this->view->form = $form;
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    /*
     * Create a meeting item 
     */
    public function createAction()
    {
        $form = new Core_Form_Activity_Meeting_Create;
        $form->setAction($this->_helper->url('create', 'meeting', 'activity'));
        if($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Product_Validate_MeetingAttendees();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid = $form->isValid($_POST);
            if($itemsValid and $formValid) {
                $meetingId = $this->_model->create($this->getRequest()->getPost());
                $url = $this->view->url(array(
                    'module'        => 'activity',
                    'controller'    => 'meeting',
                    'action'        => 'viewdetails',
                    'meeting_id'    => $meetingId
                ));
                $this->_helper->FlashMessenger('The meeting details has been created successfully');
                $this->_redirect($url);
            } else {
                $this->view->itemMessages = $itemsValidator->getAllItemsMessages();
                $this->view->includeRecreateScript = true;
                $this->view->returnedContactItemsJSON = $itemsValidator->getContactFilteredJSON();
                $this->view->returnedUserItemsJSON = $itemsValidator->getUserFilteredJSON();
                $this->view->returnedLeadItemsJSON = $itemsValidator->getLeadFilteredJSON();
                $form->populate($_POST);
            }
        }
        $this->view->form = $form;
    }
   
    /**
     * Edit a meeting entry
     */ 
    public function editAction()
    {
        $meetingId = $this->_getParam('meeting_id');
        $this->_model->setMeetingId($meetingId);
        $form = new Core_Form_Activity_Meeting_Edit($this->_model);
        $form->setAction(
            $this->_helper->url(
                'edit',
                'meeting',
                'activity',
                array(
                    'meeting_id' => $meetingId,
                )
            )
        );
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Product_Validate_MeetingAttendees();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
                $this->_model->edit($this->getRequest()->getPost());
                $this->_helper->FlashMessenger('Meeting has been edited');
                $this->_helper->redirector(
                    'viewdetails','meeting', 'activity', 
                    array('meeting_id'=>$meetingId)
                );
            } else {
                $this->view->itemMessages = $itemsValidator->getAllItemsMessages();
                $this->view->includeRecreateScript = true;
                $this->view->returnedContactItemsJSON = $itemsValidator->getContactFilteredJSON();
                $this->view->returnedUserItemsJSON = $itemsValidator->getUserFilteredJSON();
                $this->view->returnedLeadItemsJSON = $itemsValidator->getLeadFilteredJSON();
                $form->populate($_POST);
            }
        } else {

            /**
             * When the request is not post, we want to populate the form
             * using the values stored in the database
             */
            $contactItems = $this->_model->getContactItemsJson();
            $userItems = $this->_model->getUserItemsJson();
            $leadItems = $this->_model->getLeadItemsJson();
            $this->view->returnedContactItemsJSON =  Zend_Json::encode($contactItems);
            $this->view->returnedUserItemsJSON =  Zend_Json::encode($userItems);
            $this->view->returnedLeadItemsJSON =  Zend_Json::encode($leadItems);
        }
    }
 
    /**
     * View meeting details
     */

    public function viewdetailsAction()
    {
        $this->view->meeting = $this->_model->setMeetingId($this->_getParam('meeting_id'))->fetch();
        $this->view->meetingAttendees = $this->_model->setMeetingId($this->_getParam('meeting_id'))->fetchMeetingAttendees();
    }

    /**
     * Delete a meeting entry
     */

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setMeetingId($this->_getParam('meeting_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The meeting was successfully deleted'; 
        } else {
           $message = 'The meeting could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'meeting', 'activity');
    }

    public function notesAction()
    {
        $meetingId = $this->_getParam('meeting_id');
        $this->_model->setMeetingId($meetingId);
        $this->view->meetingId = $meetingId;
        $notes =  $this->_model->getNotes();
        $paginator = $notes->getPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    public function createnotesAction()
    {
        $meetingId = $this->_getParam('meeting_id');
        $this->_model->setMeetingId($meetingId);
        $model = $this->_model->getNotes();
        $form = new Core_Form_Activity_Note_Create;
        $form->setAction($this->view->url(array(
                'module' => 'activity',
                'controller' => 'meeting',
                'action' => 'createnotes',
                'meeting_id' => $meetingId,
            ), NULL, TRUE));

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $model->create($form->getValues());
                $this->_helper->FlashMessenger('Meeting note has been successfully created');
                $url = $this->view->url(array(
                    'module' => 'activity',
                    'controller' => 'meeting',
                    'action' => 'notes',
                    'meeting_id' => $meetingId,
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



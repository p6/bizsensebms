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

class NewsLetter_ListController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_List
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_Newsletter_List;
    }

    /**
     * index page
     */ 
    public function indexAction() 
    {
        $form = new Core_Form_Newsletter_List_Search;
        $form->populate($_GET);
        $this->view->form = $form;
       
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;
    
        /**
         * Notify the view if the submit was hit on the search form
         */
        if ($form->getValue('submit') == 'Search') {
            $this->view->wasSearched = true;
        }
    }

    /**
     * create new list
     */
    public function createAction()
    {
        $form = new Core_Form_Newsletter_List_Create;
         
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {    
                $listId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('List added'); 
                $this->_helper->redirector(
                    'viewdetails', 
                    'list', 
                    'newsletter',
                    array(
                        'list_id' => $listId)
                    );
            } else {
                $form->populate($_POST);
                $this->view->form = $form;                
            }
        } else {
            $this->view->form = $form;
        }
    }

    /**
     * view list
     */
    public function viewdetailsAction()
    {   
        $listId = $this->_getParam('list_id');
        $this->_model->setlistId($listId);
         
        $form = new Core_Form_Newsletter_List_Subscriber_Create($listId);
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
                $subscriberId = $listSubscriberModel->create($listId,$_POST['subscriber_id']);
                $this->_helper->FlashMessenger('The subscriber  was added successfully');
                $this->_helper->redirector(
                    'viewdetails', 
                    'list', 
                    'newsletter',
                    array(
                        'list_id' => $listId)
                    );
            } 
        }                
        $this->view->list = $this->_model->fetch();
        $this->view->listId = $listId;
    }

    /**
     * Edit Campaign item details
     */
    public function editAction()
    {
        $listId = $this->_getParam('list_id'); 
        $this->_model->setListId($listId);
        
        $form = new Core_Form_Newsletter_List_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'list', 
                'newsletter',
                array(
                    'list_id'=>$listId
                )
            )
        );
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The list has been edited successfully');
                $this->_helper->redirector('viewdetails', 'list', 'newsletter',
                    array('list_id'=>$listId));
            } else {
                $form->populate($this->getRequest()->getPost());
                $this->view->form = $form;
            }
        } else {
                $form->populate($this->getRequest()->getPost());
                $this->view->form = $form; 
        }
    }

    /**
     * Delete list details
     */
    public function deleteAction()
    {
        $listId = $this->_getParam('list_id');
        $this->_model->setListId($listId);

        $cForm = new BV_Form_Confirm;
        $action = $this->view->url(array(
            'module'        =>  'newsletter',
            'controller'    =>  'list',
            'action'        =>  'delete',
            'list_id'    =>  $listId,
        ));
        $form = $cForm->getForm();
        $form->setAction($action);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost()) and $this->_getParam('yes') == 'Yes') {
                $deleted = $this->_model->delete();
                if ($deleted) {
                    $this->_helper->FlashMessenger('List deleted');
                } else {
                    $this->_helper->FlashMessenger('List could not be deleted');
                }
                $this->_helper->redirector('index', 'list', 'newsletter' );
            } else {
                 $this->_helper->redirector('viewdetails', 'list', 'newsletter',
                    array('list_id'=>$listId));
            }
        } else {
            $listRecord = $this->_model->fetch();
            $this->view->list = $listRecord;
            $this->view->form = $form;
        }
        
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setListId($this->_getParam('list_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The list was successfully deleted'; 
        } else {
           $message = 'The list could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'list', 'newsletter');
    }

    /**
     * show subscriber list
     */
    public function subscriberAction()
    {
        $listId = $this->_getParam('list_id');
        
        $this->_model->setlistId($listId);
       
        $subscriberList = $this->_model->getSubscriber()
                                                    ->viewSubscribers($listId);
            
        $this->view->subscriberList = $subscriberList;
        $this->view->listId = $listId;
        $search['list_id'] = $listId;
        $subscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $paginator = $subscriberModel->getPaginator($search);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    /**
     * delete subscriber
     */
    public function subscriberdeleteAction()
    {
        $listId = $this->_getParam('list_id');
        $listSubscriberId = $this->_getParam('list_subscriber_id');
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $listSubscriberModel->setListSubscriberId($listSubscriberId);
        $deleted = $listSubscriberModel->delete();

        if ($deleted) {
           $message = 'The subscriber was successfully deleted'; 
        } else {
           $message = 'The subscriber could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('subscriber', 'list', 'newsletter',
            array('list_id'=>$listId)
        );            
    }

    /**
     * edit subscriber
     */   
    public function subscribereditAction()
    {
       /* $listId = $this->_getParam('list_id');
        $this->_model->setlistId($listId);
        $subscriberId = $this->_getParam('subscriber_id');
        $form = new Core_Form_Newsletter_List_Subscriber_Edit($listId,$subscriberId);
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                var_dump($this->getRequest()->getPost());
        exit();
                $subscriber =  $this->_model->getSubscriber()
                        ->setSubscriberId($subscriberId)
                        ->edit($form->getValues());
                $this->_helper->FlashMessenger('The subscriber has been edited
                                 successfully');
                $this->_helper->redirector('subscriber', 'list', 'newsletter',
                            array('list_id'=>$listId
                        ));
            } else {
                $form->populate($this->getRequest()->getPost());
                $this->view->form = $form;
            }
        } else {
                $this->view->form = $form; 
        }*/
    }
 
    /**
     * Import leads from a CSV file
     */
    public function importAction()
    {
        $listId = $this->_getParam('list_id');
        $this->_model->setListId($listId);
        $form = new Core_Form_Newsletter_List_Subscriber_Import;
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->list_import_csv->receive()) {
                    $location = $form->list_import_csv->getFileName();
                }
                $noOfAffectedRows = $this->_model->getSubscriber()
                                        ->import($location, $listId);
                if($noOfAffectedRows >= 1 ) {
                    $this->_helper->FlashMessenger("
                        $noOfAffectedRows Subscribers imported successfully");
                }
                else {
                    $this->_helper->FlashMessenger("
                        No new subscribers are added to list");
                }

                $this->_helper->redirector(
                    'index',
                    'list',
                    'newsletter'
                );
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }
        } else {
            $this->view->form = $form;
        }
    }
} 

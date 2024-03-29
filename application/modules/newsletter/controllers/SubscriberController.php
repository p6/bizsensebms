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

class NewsLetter_SubscriberController extends Zend_Controller_Action 
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
        $this->_model = new Core_Model_Newsletter_Subscriber;
    }

    /**
     * index page
     */ 
    public function indexAction() 
    {
        $form = new Core_Form_Newsletter_Subscriber_Search;
        $form->populate($_GET);
        $this->view->form = $form;
        
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
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
        $form = new Core_Form_Newsletter_Subscriber_Create;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {   
                $subscriberId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('List added'); 
                $this->_helper->redirector(
                    'viewdetails', 
                    'subscriber', 
                    'newsletter',
                    array(
                        'subscriber_id' => $subscriberId)
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
     * Edit Campaign item details
     */
    public function editAction()
    {
        $subscriberId = $this->_getParam('subscriber_id'); 
        $this->_model->setSubscriberId($subscriberId);
        
        $form = new Core_Form_Newsletter_Subscriber_Edit($subscriberId);
        
        $form->setAction($this->_helper->url(
                'edit', 
                'subscriber', 
                'newsletter',
                array(
                    'subscriber_id'=>$subscriberId
                )
            )
        );
        
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The subscriber has been edited successfully');
                $this->_helper->redirector(
                    'viewdetails', 
                    'subscriber', 
                    'newsletter',
                    array(
                        'subscriber_id' => $subscriberId)
                    );
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
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setSubscriberId($this->_getParam('subscriber_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The subscriber was successfully deleted'; 
        } else {
           $message = 'The subscriber could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'subscriber', 'newsletter');     
    }
    
    /**
     * view scubscriber details
     */
    public function viewdetailsAction()
    {
        $subscriberId = $this->_getParam('subscriber_id');
        $this->_model->setSubscriberId($subscriberId);
        $this->view->subscriber = $this->_model->fetch();
        $this->view->subscriberId = $subscriberId;
    }
    
    /*
     * json store for subscriber
     */
    public function jsonstoreAction()
    {
        $this->_helper->layout->disableLayout();
              
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('subscriber_id', $items);
        $this->_helper->AutoCompleteDojo($data);

   }
   
   /**
     * reset bounce count
     */ 
    public function resetAction() 
    {
        $subscriberId = $this->_getParam('subscriber_id');
        $this->_model->setSubscriberId($subscriberId);
        $dataToUpdate['bounce_count'] = 0;
        $result = $this->_model->edit($dataToUpdate);
        
        if($result) {
           $this->_helper->FlashMessenger('Bounct count is successfully reset to 0');
        }
        $this->_helper->redirector(
                    'viewdetails', 
                    'subscriber', 
                    'newsletter',
                    array(
                        'subscriber_id' => $subscriberId)
                    );
    }    
    
}

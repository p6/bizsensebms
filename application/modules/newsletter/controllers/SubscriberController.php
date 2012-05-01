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

/* @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

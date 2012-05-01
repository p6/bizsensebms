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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class NewsLetter_DomainblacklistController extends Zend_Controller_Action 
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
        $this->_model = new Core_Model_Newsletter_DomainBlacklist;
    }

    /**
     * index page
     */ 
    public function indexAction() 
    {
       $paginator = $this->_model->getPaginator();  
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
    }

    /**
     * create new list
     */
    public function createAction()
    {
        $form = new Core_Form_Newsletter_DomainBlacklist_Create;
         
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {    
                $domainBlacklistId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('Domain is successfully added to blacklist'); 
                $this->_helper->redirector(
                    'index', 
                    'domainblacklist', 
                    'newsletter');
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
        $domainBlacklistId = $this->_getParam('domain_blacklist_id'); 
        $this->_model->setDomainBlacklistId($domainBlacklistId);
        
        $form = new Core_Form_Newsletter_DomainBlacklist_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'domainblacklist', 
                'newsletter',
                array(
                    'domain_blacklist_id'=>$domainBlacklistId
                )
            )
        );
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The list has been edited successfully');
                $this->_helper->redirector('index', 'domainblacklist', 'newsletter');
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
                        ->setDomainBlacklistId($this->_getParam('domain_blacklist_id'))
                        ->delete();
        if ($deleted) {
           $message = 'The domain was successfully deleted from blacklist'; 
        } else {
           $message = 'The domain could not be deleted from blacklist'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'domainblacklist', 'newsletter');
    }
} 

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
class NewsLetter_QueueController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Variable
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_Newsletter_Message_Queue; 
    }

    /**
     * mail queue settings
     */
    public function settingsAction()
    {
        $form = new Core_Form_Newsletter_Queue_Settings;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {    
                $this->_model->saveSettings($form->getValues());
                $this->_helper
                        ->FlashMessenger(
                            'Message queue settings saved successfully'
                        ); 
                $this->_helper->redirector('index', 'settings', 'newsletter');
            } else {
                $form->populate($_POST);
                $this->view->form = $form;                
            }
        } else {
            $dataToPopulate = $this->_model->fetchSettings();
            $form->populate($dataToPopulate);
            $this->view->form = $form;
        }
    }

    /**
     * display message status
     */
    public function statusAction()
    {
        $this->_helper->layout->setLayout('layout_reports');
        $totalMessages = $this->_model->fetchTotalToBeSent();
        $this->view->totalMessages = $totalMessages;
        $totalMessagesWithHtmlFormat = $this->_model->fetchByHtmlFormat();
        $this->view->totalMessagesWithHtmlFormat = $totalMessagesWithHtmlFormat;
        $totalMessagesWithTextFormat = $this->_model->fetchByTextFormat();
        $this->view->totalMessagesWithTextFormat = $totalMessagesWithTextFormat;
        $totalUniqueDomains = $this->_model->fetchNumberOfUniqueDomains();
        $this->view->totalUniqueDomains = $totalUniqueDomains;

    }

    /**
     * remove all unsent message
     */
    public function cancelallAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_model->cancelAllPendingMessages(); 
        $this->_helper->FlashMessenger("Successfully cancelled all pending messages from queue");
        $this->_helper->redirector('status', 'queue', 'newsletter');
    }
    
    /**
     * remove message for unsent message
     */
    public function cancelAction()
    {
        $messageId = $this->_getParam('message_id'); 
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_model->cancelMessage($messageId);
        $this->_helper->FlashMessenger("Successfully cancelled this message from queue");
        $this->_helper->redirector('index', 'message', 'newsletter');
    }
    
    /**
     * display queue
     */
    public function indexAction()
    {
        $form = new Core_Form_Newsletter_Queue_Search;
        $form->populate($_GET);
        $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
        
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }


} 

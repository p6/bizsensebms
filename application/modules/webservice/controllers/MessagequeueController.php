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
class Webservice_MessagequeueController extends Zend_Rest_Controller
{
    protected $_service;

    public function init()
    {
        $this->_service = new Core_Service_WebService_Rest_Newsletter_Message_Queue;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * @TODO add validator to email in custom content
     */
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        $messageId = $this->_getParam('message_id');
        $response = $this->getResponse();
        $this->_service->setMessageId($messageId);
        $listForm = new Core_Form_Newsletter_Message_AddToQueue();
        if (isset($post['test_message']) and $post['test_message']) {
            $form = new Core_Form_Newsletter_Message_TestMessage();
            if ($form->isValid($post)) {
                $this->_service->sendTestMessage(
                    $post['recipient'], 
                    $post['first_name'], 
                    $post['middle_name'], 
                    $post['last_name']);
                $response->setHttpResponseCode(201);
            } else {
                $this->_helper->json($form->getMessages());
                return;
            }
        } else if(isset($post['custom_content']) and $post['custom_content']) {
            $parameterKeys = array(
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'format',
                'custom_text_body',
                'custom_html_body',
                'custom_subject',
            );
            $parameters = array();
            foreach ($parameterKeys as $key) {
                if (isset($post[$key])) {
                    $parameters[$key] = $post[$key];
                }
            }
            $success = $this->_service->addToQueue($parameters);
            if ($success) {
                $response->setHttpResponseCode(201);
                return;
            } else {
                $response->setHttpResponseCode(403);
            }
        } else if($listForm->isValid($post)) {
            $this->_service->sendToLists($listForm->getValues());
            $response->setHttpResponseCode(201);
        } else {
            $response->setHttpResponseCode(400);
            $this->_helper->json($listForm->getmessages());
        }
    }

    public function indexAction() 
    {
    }

    public function getAction()
    {
    }

    public function  putAction()
    {
    }
    
    public function deleteAction()
    {
    }

}

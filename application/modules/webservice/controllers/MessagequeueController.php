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

class Webservice_MessagequeueController extends Zend_Rest_Controller
{
    protected $_service;

    public function init()
    {
        $this->_service = new Core_Service_WebService_Rest_Newsletter_Message_Queue;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * @TODO add validator to email in custom content, @issue 1160
     * 
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

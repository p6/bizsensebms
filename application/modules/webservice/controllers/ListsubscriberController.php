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

class Webservice_ListsubscriberController extends Zend_Rest_Controller
{
    protected $_service;

    public function init()
    {
        $this->_service = new Core_Service_WebService_Rest_Newsletter_List_Subscriber;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function postAction()
    {        
        $post = $this->getRequest()->getPost();
        $response = $this->getResponse();
        $form = new Core_Form_Newsletter_List_Subscriber_Create($post['list_id']);
        if ($form->isValid($post)) {
            $result = $this->_service->create($post);
            $response->setHttpResponseCode(201);
            $this->_helper->json(array('list_subscriber_id' => $result));
        }
        else {
            $response->setHttpResponseCode(400);
            $this->_helper->json($form->getMessages());
        }
    }

    public function indexAction() 
    {
        $collection = $this->_service->fetchAll();
        $this->_helper->json($collection);
    }

    public function getAction()
    {
        $listSubscriberId = $this->_getParam('list_subscriber_id');
        $response = $this->getResponse();
        
        $record = $this->_service->fetch($listSubscriberId);
        if ($record) {
            $response->setHttpResponseCode(200);
            $this->_helper->json($record);
        } else {
            $response->setHttpResponseCode(404);
        }
 
    }

    public function putAction()
    {
        /*$listSubscriberId = $this->_getParam('list_subscriber_id');
        $response = $this->getResponse();
        $data = Zend_Json::decode($this->getRequest()->getRawBody());
        $response = $this->getResponse();
        $form = new Core_Form_Newsletter_List_Subscriber_Edit($listId);
        if ($form->isValid($data)) {
            $edited = $this->_service->edit($email, $data);
            $response->setHttpResponseCode(200);
            $this->_helper->json($edited);
        } else {
            $response->setHttpResponseCode(400);
            $this->_helper->json($form->getMessages());
        }*/
    }

    
    public function deleteAction()
    {
        $listSubscriberId = $this->_getParam('list_subscriber_id');
        $deleted = $this->_service->delete($listSubscriberId);
        if ($deleted) {
            $responseCode = 204; 
        } else {
           $responseCode = 404; 
        }
        $this->getResponse()->setHttpResponseCode($responseCode);
    }

}

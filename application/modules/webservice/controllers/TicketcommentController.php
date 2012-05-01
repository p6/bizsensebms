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

class Webservice_TicketcommentController extends Zend_Rest_Controller
{
    protected $_ticketModel;

    protected $_service;

    public function init()
    {
        $this->_ticketModel = new Core_Model_Ticket;
        $this->_service = new Core_Service_WebService_Rest_Ticket;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Create a ticket comment 
     */
    public function postAction()
    {
        $ticketId = $this->_getParam('ticket_id');
        $post = $this->getRequest()->getPost();
        $ticketId = $this->_service->setTicketId($ticketId)->getComment()->create($post);
        $this->getResponse()->setHttpResponseCode(201);
        $responseData = array(
            'url' => 'ticket/ticket_id/' . $ticketId
        );
        $this->_helper->json($responseData);
        
    }

    public function indexAction() 
    {
        $criteria = $this->_getParam('criteria');
        if ($criteria == 'ticket') {
            $ticketId = $this->_getParam('ticket_id');
            $service = new Core_Service_WebService_Rest_Ticket();
            $service->setTicketId($ticketId);
            $comment = $service->getComment();
            $tickets = $comment->fetchAll();
            $this->_helper->json((array) $tickets);
        }
    }

    public function getAction()
    {
        $ticketId = $this->_getParam('id');
        $this->_service->setTicketId($ticketId);
        $this->_helper->json((array) $this->_service->getComment()->fetchAll());
    }

    public function  putAction()
    {
        echo "put action";
    }

    public function deleteAction()
    {
        echo "delete action";
    }

}

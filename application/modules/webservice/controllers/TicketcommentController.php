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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

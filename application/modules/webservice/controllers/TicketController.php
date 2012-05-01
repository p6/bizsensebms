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
class Webservice_TicketController extends Zend_Rest_Controller
{
    protected $_model;
    protected $_service;

    public function init()
    {
        $this->_model = new Core_Model_Ticket;
        $this->_service = new Core_Service_WebService_Rest_Ticket;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Create a ticket 
     */
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        $ticketId = $this->_service->create($post);
        $ticketRecord = $this->_model->setTicketId($ticketId)->fetch();
        $this->getResponse()->setHttpResponseCode(201);
        $url = 'ticket/ticket_id/' . $ticketId;
        $this->getResponse()->setHeader('location', $url);
        $this->_helper->json($ticketRecord);
        
    }

    public function indexAction() 
    {
        $criteria = $this->_getParam('criteria');
        if ($criteria == 'by_contact_email') {
            $contactEmail = $this->_getParam('contact_email');
            $tickets = $this->_service->fetchAllByContactEmail($contactEmail);
            $this->_helper->json((array) $tickets);
        }
    }

    public function getAction()
    {
        $ticketId = $this->_getParam('id');
        $this->_service->setTicketId($ticketId);
        $this->_helper->json((array) $this->_service->fetch());
    }

    public function  putAction()
    {    
        $ticketId = $this->_getParam('id');
        $this->_service->setTicketId($ticketId);
        $data = Zend_Json::decode($this->getRequest()->getRawBody());
        $this->_service->edit($data);
        $this->_helper->json((array) $this->_service->fetch());
    }

    public function deleteAction()
    {
    }

}

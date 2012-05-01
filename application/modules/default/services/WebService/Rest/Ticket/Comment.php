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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 */

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Service_WebService_Rest_Ticket_Comment extends Core_Model_Abstract
{
   
    /**
     * Comment poster is the contact
     */
    const COMMENT_POSTER_TYPE_CONTACT = 1;

    /**
     * Comment poster is a BizSense user
     */
    const COMMENT_POSTER_TYPE_USER = 2;

    const STATUS_CREATE = 'comment created';
    
    /**
     * @see BV_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_TicketComment';

    /**
     * @var
     */
    protected $_defaultObservers = array(
        'Core_Service_WebService_Rest_Ticket_Comment_Notify_Email',
    );

    /**
     * @var the ticket model
     */
    protected $_model;

    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @var the ticket service
     */
    protected $_service;

    /**
     * @var the ticket comment id
     */
    protected $_ticketCommentId;

    /**
     * @param object $service the ticket service
     * @return fluent interface
     */
    public function setService($service)
    {
        $this->_service = $service;
        return $this;
    }

    /**
     * @return object Core_Service_Ticket
     */
    public function getService()
    {
        return $this->_service;
    }

    /**
     * @param int $ticketCommentId the ticket comment id
     * @return fluent interface
     */
    public function setTicketCommentId($ticketCommentId)
    {
        $this->_ticketCommentId = $ticketCommentId;
        return $this;
    }
 
    /**
     * @return int the ticket comment ID
     */
    public function getTicketCommentId()
    {
       return $this->_ticketCommentId; 
    }

    /**
     * Creates a row in the contact table
     */
    public function create($data = array())
    {
        $dataToInsert = array(
            'ticket_id' => $this->_service->getTicketId(),
            'title' => $data['title'],
            'description' => $data['description'],
            'created' => time(),
            'created_by_type' => self::COMMENT_POSTER_TYPE_CONTACT,
        );
        $table = $this->getTable();
        $result = $table->insert($dataToInsert);
        $this->setTicketCommentId($result);
        $this->setStatus(self::STATUS_CREATE);
        return $result;
    }

    /**
     * @return array collection of ticket comments
     */
    public function fetchAll()
    {
        $table = $this->getTable();
        $ticketId = $this->_service->getTicketId();
        $select = $table->select()->where('ticket_id = ?', $ticketId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     *
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('ticket_comment_id = ?', $this->_ticketCommentId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;

    }
   
}



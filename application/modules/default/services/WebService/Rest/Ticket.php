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

class Core_Service_WebService_Rest_Ticket extends Core_Model_Abstract
implements Core_Model_Observable_Interface
{

    const STATUS_CREATE = 'tickted created';
    const STATUS_EDIT = 'ticket edited';
    const STATUS_DELETE = 'ticket deleted';
    const STATUS_EDITSTATUS = 'ticket status changed';

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Ticket';


    /**
     * @see Core_Model_Abstract::defaultObservers
     */
    protected $_defaultObservers = array(
        'Core_Service_WebService_Rest_Ticket_Notify_Email',
    );

    /**
     * @var int the ticket ID
     */
    protected $_ticketId;

    /**
     * @var object Core_Model_Ticket
     */
    protected $_ticketModel;


    /**
     * @param int $ticketId
     * return fluent interface
     */
    public function setTicketId($ticketId)
    {
        $this->_ticketId = $ticketId; 
        return $this;
    }

    /**
     * @return int ticket ID
     */
    public function getTicketId()
    {
        return $this->_ticketId;
    }

    /**
     * @param string $contactemail the work email address of the contact
     * @return array the result of the fetched rows
     */
    public function fetchAllByContactEmail($contactEmail)
    {
        $contact = new Core_Model_Contact;
        $contactData = $contact->findByWorkEmail($contactEmail);
        $contactId = $contactData['contact_id'];
        $table = $this->getTable();
        $select = $table->select()->where('contact_id = ?', $contactId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return array ticket record
     */
    public function fetch()
    {   
        $table = $this->getTable();
        $select = $table->select()->where('ticket_id = ?', $this->_ticketId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return object Core_Service_Ticket
     */
    public function getComment()
    {
        $comment = new Core_Service_WebService_Rest_Ticket_Comment();
        $comment->setService($this);
        return $comment;
    }

    /**
     * Creates a row in the contact table
     */
    public function create($data = array())
    {
        $table = $this->getTable();

        $ticketModel = $this->getModel();
        $defaultAssigneeId = $ticketModel->getDefaultAssigneeId();
        if (is_numeric($defaultAssigneeId)) {
            $data['assigned_to'] = $defaultAssigneeId;
        }

        $contact = new Core_Model_Contact;
        $contactData = $contact->findByWorkEmail($data['contact_email']);
        unset($data['contact_email']);
        $data['contact_id'] = $contactData['contact_id'];
        $data['created'] = time();

        $result = $table->insert($data);

        $this->getModel()->setTicketId($result);
        $this->setTicketId($result);

        $this->setStatus(self::STATUS_CREATE);

        return $result;
    }

    /**
     * Creates a row in the contact table
     */
    public function edit($data = array())
    {
        $ticketRecord = $this->fetch();
        $ticketStatusBeforeEdit = $ticketRecord['ticket_status_id'];
        $ticketStatusAfterEdit = $data['ticket_status_id'];
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('ticket_id = ?', $this->_ticketId);
        $result = $table->update($data, $where);
        
        if ($ticketStatusBeforeEdit != $ticketStatusAfterEdit) {
            $this->setStatus(self::STATUS_EDITSTATUS);
        }
        return $result;
    }

    public function getModel()
    {
        if (!$this->_ticketModel) {
            $this->_ticketModel = new Core_Model_Ticket();
        }
        return $this->_ticketModel;
    }
}



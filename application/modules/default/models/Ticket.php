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

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Ticket extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';

    const VARIABLE_KEY_DEFAULT_ASSIGNEE_ID = 'core_default_ticket_assignee_id';

    protected $_ticketId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Ticket';

    
    /**
     * Sets the status upon CRUD actions
     * @param CONST STATUS_?
     * Calls Contact_Notify_Email::update
     */
    public function setStatus($status = null)
    {
        $this->_status = $status;
    }

    /**
     * @param int $ticketId the ticket ID
     * @return fluent interface
     */
    public function setTicketId($ticketId)
    {
        $this->_ticketId = $ticketId;
        return $this;
    }

    /**
     * @return int the ticket ID
     */
    public function getTicketId()
    {
        return $this->_ticketId;
    }

    /**
     * @return array the ticket record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->join('ticket_status', 
                                'ticket_status.ticket_status_id = 
                                    ticket.ticket_status_id',
                                array('ticket_status.name as ticket_status')
                            )
                        ->where('ticket_id = ?', $this->_ticketId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;

    }

    /**
     * Creates a row in the ticket table
     * @param array $data
     * @return int ticket ID
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $result = $table->insert($data);
        return $result;
    }

    /**
     * Updates a row in the ticket table
     * @param array $data
     * @return int ticket ID
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('ticket_id = ?', $this->_ticketId);
        $result = $table->update($data, $where);
        return $result;
    }

    /**
     * Delete a row in the ticket table
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('ticket_id = ?', $this->_ticketId);
        $result = $table->delete($data, $where);
        return $result;
    }
  

    /**
     * @return object Core_Model_Ticket_Comment
     */
    public function getComment()
    {
        $comment = new Core_Model_Ticket_Comment();
        $comment->setModel($this);
        return $comment;
    }


    /**
     * Get the default ticket assignee
     * @param int user ID
     */
    public function getDefaultAssigneeId()
    {
        $variable = new Core_Model_Variable(self::VARIABLE_KEY_DEFAULT_ASSIGNEE_ID);
        return $variable->getValue();
    }

    /**
     * Set the default lead assignee
     * @param int $userId the user id
     */
    public function setDefaultAssigneeId($userId)
    {    
        $variable = new Core_Model_Variable();
        $variable->save(self::VARIABLE_KEY_DEFAULT_ASSIGNEE_ID, $userId);

    }

    /**
     * Set all unassigned leads to this user id
     * @param int $userId the user id
     */
    public function assignUnassignedTicketsTo($userId)
    {
        $table = $this->getTable();
        $where = "assigned_to is NULL";
        $data = array(
            'assigned_to' => $userId
        );
        return $table->update($data, $where);
    }

}



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



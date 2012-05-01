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

class Core_Model_Ticket_Status extends Core_Model_Abstract
{
    const TICKET_STATUS_CONTEXT_CLOSED = 1;
    const TICKET_STATUS_CONTEXT_OPEN = 0;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_TicketStatus';


    protected $_ticketStatus;
    
    public function setTicketStatusId($ticketStatusId)
    {
        $this->_ticketStatusId = $ticketStatusId;
        return $this;
    }

    public function getTicketStatusId()
    {
        return $this->_ticketStatusId;
    }

    public function fetchAll()
    {
        $table = $this->getTable();
        $result = $table->fetchAll();
        if ($result) {
            return $result->toArray();
        } else {
            return array();
        }
    }

    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('ticket_status_id = ?', $this->_ticketStatusId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;

    }
    /**
     * Creates a row in the contact table
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $result = $table->insert($data);
        return $result;
    }

    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto(
                        'ticket_status_id = ?', $this->_ticketStatusId
                        );
        return $table->delete($where);
    }

    /**
     * Updates a row in the ticket status table
     * @param array $data
     * @return int ticket status id
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto(
                        'ticket_status_id = ?', $this->_ticketStatusId
                        );
        $result = $table->update($data, $where);
        return $result;
    }

}



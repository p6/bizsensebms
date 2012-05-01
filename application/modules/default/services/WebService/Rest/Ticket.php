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

interface
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



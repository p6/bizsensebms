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

class Core_Model_Ticket_Comment extends Core_Model_Abstract
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
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_TicketComment';

    /**
     * @var int the comment ID
     */
    protected $_ticketCommentId;

    /**
     * @var the ticket model
     */
    protected $_model;

    protected $_defaultObservers = array(
        'Core_Model_Ticket_Comment_Notify_Email'
    );

    /**
     * @param object Core_Model_Ticket
     * @return fluent interface
     */
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @return object Core_Model_Ticet
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param int $commentId
     * @return fluent interface
     */
    public function setTicketCommentId($ticketCommentId)
    {
        $this->_ticketCommentId = $ticketCommentId;
        return $this;
    }
    
    /**
     * @return int the comment ID
     */
    public function getCommentId()
    {
        return $this->_ticketCommentId;
    }

    /**
     * @return array the comment record
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
  
    /**
     * Creates a row in the contact table
     * @param array $data
     * @return the comment ID
     */
    public function create($data = array())
    {
        $dataToInsert = array(
            'ticket_id' => $this->_model->getTicketId(),
            'title' => $data['title'],
            'description' => $data['description'],
            'created' => time(),
            'created_by_type' => self::COMMENT_POSTER_TYPE_USER,
            'user_id' => Core_Model_User_Current::getId(),
        );
        $table = $this->getTable();
        $result = $table->insert($dataToInsert);
        $this->setTicketCommentId($result);
        $this->setStatus(self::STATUS_CREATE);
        return $result;
    }

    /**
     * @return array the collection of comments
     */
    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('ticket_id = ?', $this->_model->getTicketId())
                    ->order('ticket_comment_id ASC');
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
}



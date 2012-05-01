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



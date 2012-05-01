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



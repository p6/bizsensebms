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

class Core_Service_WebService_Rest_Ticket_Status extends Core_Model_Abstract
{

    /**
     * @var the ticket status model
     */
    protected $_model;

    /**
     * @return object Core_Model_Ticket_Status
     */
    public function getModel()
    {
        if (!$this->_model) {
            $this->_model = new Core_Model_Ticket_Status();
        }
        return $this->_model;
    }
  
    public function fetchAll()
    {
        return $this->getModel()->fetchAll();
    } 

    /**
     * @return array ticket status record
     */
    public function setTicketStatusId($ticketStatusId)
    {
        $this->getModel()->setTicketStatusId($ticketStatusId);
    }

    public function fetch()
    {
        $return = $this->_model->fetch();
        return $return;

    }
}



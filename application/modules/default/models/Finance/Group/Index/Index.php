<?php
/*
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
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending 
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

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Ticket_Index extends Core_Model_Index_Abstract
{
    /**
     * @return Zend_Paginator object
     */
    public function getPaginator()
    {
        $table = $this->_model->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false);
        $select->join('ticket_status', 
                    'ticket_status.ticket_status_id = ticket.ticket_status_id',
                    array('ticket_status.name as ticket_status')
                );
        $paginator = 
        new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }

}

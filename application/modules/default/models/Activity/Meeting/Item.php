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

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category BizSense
 * @package  Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Activity_Meeting_Item extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Activity_MeetingAttendee';

    /**
     * @var object the meeting model
     */
    protected $_meetingModel;

    /**
     * @param object Core_Model_Activity_Meeting
     * @return object Core_Model_Activity_Meeting_Item
     */
    public function setMeetingModel($meetingModel)
    {
        $this->_meetingModel = $meetingModel;
        return $this;
    }

    /**
     * Delete all the items of the given invoice
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('meeting_id = ?', $this->_meetingModel->getMeetingId());
        $table->delete($where);
    }
}


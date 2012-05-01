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
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Activity_Meeting_Notes extends Core_Model_Abstract
{
    /**
     * @var the meeting_notes_id on which we are operating
     */
    protected $_meetingNotesId;

    /**
     * @var Zend_Db_Table object 
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Activity_MeetingNotes';

    /**
     * @param object meeting the Core_Model_Activity_Meeting
     * @return object Core_Model_Activity_Meeting_Notes
     */
    public function setModel($meeting)
    {
        $this->_model = $meeting;
        return $this;
    }

    /*
     * Inserts a row in the account_notes table
     * @param array $data with keys
     * keys - note, meeting_id   
     */
    public function create($data = array())
    {
		$data['created'] =  time();
        $data['meeting_id'] = $this->_model->getMeetingId();
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        $result = parent::create($data);
        return $result;
    }

    /**
     * @return array meeting notes record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('meeting_notes_id = ?', $this->_meetingNotesId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return Zend_Paginator object
     */
    public function getPaginator($sort = null, $search = null)
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where('meeting_id = ?', $this->_model->getMeetingId());
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }   
}
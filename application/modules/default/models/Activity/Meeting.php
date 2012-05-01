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

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Activity_Meeting extends Core_Model_Abstract
{
     /**
     * Status constants
     */
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';

    const CONTACT_ID = 1;
    const USER_ID = 2;
    const LEAD_ID = 3;
    
    /**
     * @var int meeting ID
     */
    protected $_meetingId;
    protected $_itemModel;
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Activity_Meeting';

    /**
     * @var array the default observers
     */
    protected $_defaultObservers = array(
        'Core_Model_Activity_Meeting_Notify_Email'
    );

    /**
     * @param int meetingId
     */
    public function __construct($meetingId = null)
    {
        if (is_numeric($meetingId)) {
            $this->_meetingId = $meetingId;
        }
        parent::__construct($meetingId);
    }

    /**
     * @var int set meeting ID
     */
    public function setMeetingId($meetingId)
    {
        if (is_numeric($meetingId)) {
            $this->_meetingId = $meetingId;
        }
        return $this;
    }

    /**
     * @var int get meeting ID
     */
    public function getMeetingId()
    {
        return $this->_meetingId;
    }

    /**
     * Create a meeting entry
     * @param array $data
     * @return int meeting ID
     */
    public function create($data = array())
    {
        $originalData = $data;
        unset($data['submit']);
        unset($data['contact_id']);
        unset($data['user_id']);
        unset($data['lead_id']);
        $table = $this->getTable();

        $fullStartDate = $data['start_date'].$data['start_time'];
        $startDate = new Zend_Date($fullStartDate);
        $startTimeStamp = $startDate->getTimeStamp();

        $fullEndDate = $data['end_date'].$data['end_time'];
        $endDate = new Zend_Date($fullEndDate);
        $endTimeStamp = $endDate->getTimeStamp();

        unset($data['start_time']);
        unset($data['end_time']);
        $data['start_date'] = $startTimeStamp;
        $data['end_date'] = $endTimeStamp;
        $data['created'] = time();
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        $result = $table->insert($data);
        $this->setMeetingId($result);
        $attendeeData = $this->filterInputData($originalData);
        $this->setStatus(self::STATUS_CREATE);
        return $result;
    }

    /**
     * @return array the meeting record
     */
    public function fetch()
    {
        if (!is_numeric($this->_meetingId)){
            return false;
        }
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('meeting'=>'meeting'))
            ->where('meeting_id = ?', $this->_meetingId)
            ->join('meeting_status', 
                'meeting_status.meeting_status_id = meeting.meeting_status_id',
                 'name as statusname')
            ->join('user', 'user.user_id = meeting.created_by', 'email as createdby')
            ->join('user', 'meeting.assigned_to = user.user_id', 'email');
        $result =  $table->fetchRow($select);
        if($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return array the contacts attending the meeting
     */
    public function fetchContactAttendee()
    {
        if (!is_numeric($this->_meetingId)){
            return false;
        }
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('meeting'=>'meeting'))
            ->where('meeting.meeting_id = ?', $this->_meetingId)
            ->where('meeting_attendee.attendee_type = ?', self::CONTACT_ID)
            ->join('meeting_attendee', 
        'meeting.meeting_id = meeting_attendee.meeting_id', 'attendee_id')
            ->join('contact', 'contact.contact_id = meeting_attendee.attendee_id', 
        array('work_email as email', 'first_name', 'middle_name', 'last_name'));
        $result =  $table->fetchAll($select)->toArray();
        return $result;
    }

    /**
     * @return array the users attending the meeting
     */
    public function fetchUserAttendee()
    {
        if (!is_numeric($this->_meetingId)){
            return false;
        }
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('meeting'=>'meeting'))
            ->where('meeting.meeting_id = ?', $this->_meetingId)
            ->where('meeting_attendee.attendee_type = ?', self::USER_ID)
            ->join('meeting_attendee', 
            'meeting.meeting_id = meeting_attendee.meeting_id', 'attendee_id')
            ->join('user', 
            'user.user_id = meeting_attendee.attendee_id', 'email')
            ->join('profile', 'user.user_id = profile.user_id', 
            array('first_name', 'last_name', 'middle_name'));
        $result =  $table->fetchAll($select)->toArray();
        return $result;
    }

    /**
     * @return array the leads attending the meeting
     */
    public function fetchLeadAttendee()
    {
        if (!is_numeric($this->_meetingId)){
            return false;
        }
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('meeting'=>'meeting'))
            ->where('meeting.meeting_id = ?', $this->_meetingId)
            ->where('meeting_attendee.attendee_type = ?', self::LEAD_ID)
            ->join('meeting_attendee', 
            'meeting.meeting_id = meeting_attendee.meeting_id', 'attendee_id')
            ->join('lead', 'lead.lead_id = meeting_attendee.attendee_id', 
            array('email', 'first_name', 'last_name', 'middle_name'));
        $result =  $table->fetchAll($select)->toArray();
        return $result;
    }

    /**
     * @return array the leads, contacts, users attending the meeting
     */
    public function fetchMeetingAttendees()
    {
        $result[] = $this->fetchContactAttendee();
        $result[] = $this->fetchUserAttendee();
        $result[] = $this->fetchLeadAttendee();
        $completeArray = array();
        foreach($result as $resultArray)
        {
            foreach($resultArray as $itemArray) {
                $completeArray[] = $itemArray;
            }
        }
        return $completeArray;
    }   

    /**
     * @param array $data
     * @return int 
     */
    public function edit($data = null)
    {
        $table = $this->getTable();
        $originalData = $data;
        unset($data['submit']);
        unset($data['contact_id']);
        unset($data['user_id']);
        unset($data['lead_id']);
        $fullStartDate = $data['start_date'].$data['start_time'];
        $startDate = new Zend_Date($fullStartDate);
        $startTimeStamp = $startDate->getTimeStamp();

        $fullEndDate = $data['end_date'].$data['end_time'];
        $endDate = new Zend_Date($fullEndDate);
        $endTimeStamp = $endDate->getTimeStamp();

        unset($data['start_time']);
        unset($data['end_time']);
        $data['start_date'] = $startTimeStamp;
        $data['end_date'] = $endTimeStamp;
        $data['created_by'] = $this->getCurrentUser()->getUserId();

        $where = $table->getAdapter()->quoteInto('meeting_id = ?', $this->_meetingId);
        $this->getItemModel()->setMeetingModel($this)->deleteAll();
        $result = $table->update($data, $where);
        $attendeeData = $this->filterInputData($originalData);
        $this->setStatus(self::STATUS_EDIT);
        return $result;
    }

    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select();
        $result = $table->fetchAll($select);
        return $result; 
    }   

    /**
     * @return int the number of records deleted
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('meeting_id = ?', $this->_meetingId);
        $result = $table->delete($where);
        $this->setStatus(self::STATUS_DELETE);
        return $result;
    }

    /**
     * Create meeting attendees
     * @param array $data
     * @return array
     */
    public function filterInputData($data)
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        foreach($data as $keyArray=>$valueArray) {
            if(is_array($valueArray)) {
                switch ($keyArray) {
                    case "contact_id":
                        $attendeeType = self::CONTACT_ID;
                    break;
                    case "user_id":
                        $attendeeType = self::USER_ID;
                    break;
                    case "lead_id":
                        $attendeeType = self::LEAD_ID;
                    break;
                }
                foreach($valueArray as $key=>$value) {
                    $dataArray[] = $value;
                }
                foreach ($dataArray as $key=>$value){
                    $newArray = array(
                    'meeting_id'       =>  $this->_meetingId,
                    'attendee_type'       =>  $attendeeType,
                    'attendee_id'       =>  $value,
                );
                $db->insert('meeting_attendee', $newArray);
                }
                $dataArray = null;
            }
        }
        return $dataArray;
    }

    /**
     * @return object Core_Model_Activity_Meeting_Item
     */
    public function getItemModel()
    {
        if (null === $this->_itemModel) {
            $this->_itemModel = new Core_Model_Activity_Meeting_Item;
        }
        return $this->_itemModel;
    }

    public function getContactItemsJson()
    {
        $items = $this->getItems();
        $itemToReturn = array();
        foreach ($items as $item) {
            if($item['attendee_type'] == self::CONTACT_ID) {
                $temp = $item;
                $temp['contact_id'] = $temp['attendee_id'];
                $itemToReturn[] = $temp;
            }
        }
        return $itemToReturn;
    }

    public function getUserItemsJson()
    {
        $items = $this->getItems();
        $itemToReturn = array();
        foreach ($items as $item) {
            if($item['attendee_type'] == self::USER_ID) {
                $temp = $item;
                $temp['user_id'] = $temp['attendee_id'];
                $itemToReturn[] = $temp;
            }
        }
        return $itemToReturn;
    }

    public function getLeadItemsJson()
    {
        $items = $this->getItems();
        $itemToReturn = array();
        foreach ($items as $item) {
            if($item['attendee_type'] == self::LEAD_ID) {
                $temp = $item;
                $temp['lead_id'] = $temp['attendee_id'];
                $itemToReturn[] = $temp;
            }
        }
        return $itemToReturn;
    }

    /**
     * @return array the meeting attendees
     */
    public function getItems()
    {
        $table = $this->getItemModel()->getTable();
        $select = $table->select();
        $select->where('meeting_id = ?', $this->_meetingId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return array the number of meeting records fetched related to leads 
     */
    public function fetchLeads($leadId)
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('meeting'=>'meeting'))
            ->join('profile', 'profile.user_id = meeting.assigned_to', 
            array('first_name', 'middle_name', 'last_name'))
            ->join('meeting_attendee', 
            'meeting_attendee.meeting_id = meeting.meeting_id', 'attendee_type')
            ->where('meeting_attendee.attendee_type = ?', self::LEAD_ID)
            ->where('meeting_attendee.attendee_id = ?', $leadId);
        $result = $table->fetchAll($select);
        if($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return array the number of meeting records fetched related to contacts
     */
    public function fetchContacts($contactId)
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('meeting'=>'meeting'))
            ->join('profile', 'profile.user_id = meeting.assigned_to', 
            array('first_name', 'middle_name', 'last_name'))
            ->join('meeting_attendee', 
            'meeting_attendee.meeting_id = meeting.meeting_id', 'attendee_type')
            ->where('meeting_attendee.attendee_type = ?', self::CONTACT_ID)
            ->where('meeting_attendee.attendee_id = ?', $contactId);
        $result = $table->fetchAll($select);
        if($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return object Core_Model_Activity_Meeting_Notes
     */
    public function getNotes()
    {
        $notes = new Core_Model_Activity_Meeting_Notes();
        $notes->setModel($this);
        return $notes;
    }

    public function getMeetings()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('m' => 'meeting'),
            array('m'=>'*'))
            ->where('assigned_to = ?', $this->getCurrentUser()->getUserId())
            ->join('meeting_status', 'meeting_status.meeting_status_id=m.meeting_status_id', 'context')
            ->where('context = ?', 0)
            ->order(array('created DESC'))
            ->limit(5, 0);
        $result = $table->fetchAll($select);
        return $result;

    }

}

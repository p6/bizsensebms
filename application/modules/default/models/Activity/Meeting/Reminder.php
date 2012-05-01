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
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Activity_Meeting_Reminder extends Core_Model_Abstract
{
    const MEETING_REMINDER_STATUS_NONE = 0;
    const MEETING_REMINDER_STATUS_FIVE_MINUTE = 1;
    const MEETING_REMINDER_STATUS_FIFTEEN_MINUTE = 2;
    const MEETING_REMINDER_STATUS_THIRTY_MINUTE = 3;
    const MEETING_REMINDER_STATUS_ONE_HOUR = 4;
    const MEETING_REMINDER_STATUS_ONE_DAY = 5;
    const MEETING_REMINDER_STATUS_ONE_WEEK = 6;

    const CONTACT_ID = 1;
    const USER_ID = 2;
    const LEAD_ID = 3;

    /**
     * @var object meeting record
     */
    protected $_meetingRecord;

    /**
     * @var int meeting status ID
     */
    protected $_meetingId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Activity_Meeting';

    /**
     * Execute cron
     */
    public function cron()
    {
        $this->process();
    }

    public function process()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('meeting'=>'meeting'))
            ->where('reminder_sent = ?', 0)
            ->join('user', 'meeting.assigned_to = user.user_id', 'email')
            ->join('profile', 'meeting.assigned_to = profile.user_id', 
                array('first_name', 'middle_name', 'last_name'));
        $result = $table->fetchAll($select);
        
        foreach ($result as $row) {
            $this->_meetingRecord = $row->toArray();
            $record = $this->_meetingRecord;
            $differenceTime = $record['end_date'] - time();
            $reminderSent = $record['reminder_sent'];
            switch ($record['reminder']){
            case self::MEETING_REMINDER_STATUS_FIVE_MINUTE :
                if(($differenceTime <= 400) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::MEETING_REMINDER_STATUS_FIFTEEN_MINUTE :
                if(($differenceTime >= (60*20)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::MEETING_REMINDER_STATUS_THIRTY_MINUTE :
                if(($differenceTime >= (60*35)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::MEETING_REMINDER_STATUS_ONE_HOUR :
                if(($differenceTime >= (60*70)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::MEETING_REMINDER_STATUS_ONE_DAY :
                if(($differenceTime >= (60*60*25)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::MEETING_REMINDER_STATUS_ONE_WEEK :
                if(($differenceTime >= (60*60*24*8)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::MEETING_REMINDER_STATUS_NONE :
            break;
            }
        }
        return $result;
    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendReminder()
    {
       $record = $this->_meetingRecord;
        foreach($record as $key=>$value) {
            $this->_meetingId = $record['meeting_id'];
            $allAttendees = $this->fetchMeetingAttendees();
        }
        foreach($allAttendees as $meetingRecordArray) {
            if(!empty($meetingRecordArray['email'])) {
                $recepientFullName = $meetingRecordArray['first_name'] . 
                                " " . $meetingRecordArray['middle_name'] . 
                                " " . $meetingRecordArray['last_name'];
                $recepient = $meetingRecordArray['email'];
                $subject = 'Meeting Reminder';
                $url = Core_Model_Site::getUrl();
                $url .= '/activity/meeting/viewdetails/meeting_id/' . 
                        $record['meeting_id'];
                $textTable = $this->getTextTable();
                $message = 'Hello ' . $recepientFullName . ', '
                    . "\n" . "\n";
                $contentPiece = ' There is a meeting pending by you. 
                                    To access the meeting go to '
                    . $url . '. ' .  "\n" . "\n"
                    . 'Here is a summary of the meeting '
                    . "\n" . "\n";
                $contentPiece = wordwrap($contentPiece, 80, "\n", true);
                $message .= $contentPiece;
                $message .=  $textTable .  "\n";
                $this->sendEmail($recepient, 
                                    $recepientFullName, 
                                    $message, 
                                    $subject);
                $this->update();
            }   
        }
    }

    public function update()
    {
        $table = $this->getTable();
        $record = $this->_taskRecord;
        $data = array('reminder_sent'=>'1');
        $where = $table->getAdapter()->quoteInto('call_id = ?', $record['call_id']);
        $table->update($data, $where);
    }


    public function fetchContactAttendee()
    {
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

    public function fetchUserAttendee()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('meeting'=>'meeting'))
            ->where('meeting.meeting_id = ?', $this->_meetingId)
            ->where('meeting_attendee.attendee_type = ?', self::USER_ID)
            ->join('meeting_attendee', 
                'meeting.meeting_id = meeting_attendee.meeting_id', 'attendee_id')
            ->join('user', 'user.user_id = meeting_attendee.attendee_id', 'email')
            ->join('profile', 'user.user_id = profile.user_id', 
                array('first_name', 'last_name', 'middle_name'));
        $result =  $table->fetchAll($select)->toArray();
        return $result;
    }

    public function fetchLeadAttendee()
    {
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

    public function fetchMeetingAttendees()
    {
        $result[] = $this->fetchContactAttendee();
        $result[] = $this->fetchUserAttendee();
        $result[] = $this->fetchLeadAttendee();
        foreach($result as $resultArray)
        {
            foreach($resultArray as $itemArray) {
                $completeArray[] = $itemArray;
            }
        }
        return $completeArray;
    }   


    /**
     * @return object Zend_Text_Table
     */
    public function getTextTable()
    {
        $meetingRecord = $this->_meetingRecord;

        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table(
                array('columnWidths' => array(20, 30)))
        ;
        $textTable->appendRow(
                array('Subject', $meetingRecord['name'])
        );
        $textTable->appendRow(
                array('Description', $meetingRecord['description'])
        );
        $textTable->appendRow(
                array('Assigned to', $meetingRecord['email'])
        );
        return $textTable;
    }



    /**
     * Sends the email and adds the recipient to sent history
     * @param string $recipient email recipient
     * @param string $message email message
     */
    public function sendEmail($recipient, $recipientFullName, $message, $subject)
    {
        $subject .= " - BizSense";
        $mail = new Core_Service_Mail;
        $mail->setBodyText($message);
        $mail->addTo($recipient, $recipientFullName);
        $mail->setSubject($subject);
        $mail->send();
    }
}
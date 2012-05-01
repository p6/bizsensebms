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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Activity_Meeting_Notify_Email implements Core_Model_Observer_Interface
{
    /**
     * @var object Core_Model_Activity_Meeting
     */
    protected $_meeting;

    /**
     * @var object meeting record
     */
    protected $_meetingRecord;

    /**
     * @var array recipients
     */
    protected $_recipients = array();    

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
                array('Subject', $meetingRecord[0]['name'])
        );
        $textTable->appendRow(
                array('Description', $meetingRecord[0]['description'])
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
        if (!in_array($recipient, $this->_recipients)) {
            try  {  
                $mail->send();
             }   
            catch (Zend_Exception $e) {
                $log = new Core_Service_Log;
                $info = 'Failed in connecting mail server';
                $log->info($info);
            }
            $this->_recipients[] = $recipient;
        }
    }

    /**
     * @return object Core_Model_User
     */
    public function getCurrentUser()
    {
        return Zend_Registry::get('user');
    }

    /**
     * @param object $meeting Core_Model_Activity_Meeting
     */
    public function update($meeting)
    {  
        $this->_meeting = $meeting;
        $meetingRecord = $meeting->fetchMeetingAttendees();

        $this->_meetingRecord = $meetingRecord;    
        $meetingStatus = $meeting->getStatus();
        switch ($meetingStatus) {
            case Core_Model_Activity_Meeting::STATUS_CREATE :
                $this->composeAndSendCreated();
                break;
            case Core_Model_Activity_Meeting::STATUS_EDIT :
                $this->composeAndSendEdited();
                break;
            case Core_Model_Activity_Meeting::STATUS_DELETE :
                $this->_meetingRecord = $meeting->getEphemeral();
                $this->composeAndSendDeleted();
                break;
        }
    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendCreated()
    {
        $meetingRecord = $this->_meetingRecord;
        foreach($meetingRecord as $meetingRecordArray) {
            if(!empty($meetingRecordArray['email'])) {
                $subject = 'New Meeting';
                $createdByUser = new Core_Model_User($meetingRecordArray['created_by']);
                $url = Core_Model_Site::getUrl();
                $url .= '/activity/meeting/viewdetails/meeting_id/' . $meetingRecordArray['meeting_id'];

                $textTable = $this->getTextTable();

                $fullName = $meetingRecordArray['first_name'] . " " . $meetingRecordArray['middle_name'] . " " . $meetingRecordArray['last_name'];
                $message = 'Hello ' . $fullName . ', ' 
                    . "\n" . "\n";
                $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
                    . $createdByUser->getEmail() . ')'
                    . ' created a new meeting. To access the meeting go to ' 
                    . $url . '. ' .  "\n" . "\n" 
                    . 'Here is a summary of the meeting ' 
                    . "\n" . "\n";
                $contentPiece = wordwrap($contentPiece, 80, "\n", true);
                $message .= $contentPiece;
                $message .=  $textTable .  "\n"; 
                $this->sendEmail($meetingRecordArray['email'], $fullName, $message, $subject);
            }
        }
    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendEdited()
    {
        $meetingRecord = $this->_meetingRecord;
        foreach($meetingRecord as $meetingRecordArray) {
            if(!empty($meetingRecordArray['email'])) {

                $subject = 'Meeting has been edited';
                $createdByUser = new Core_Model_User($meetingRecordArray['created_by']);
                $url = Core_Model_Site::getUrl();
                $url .= '/activity/meeting/viewdetails/meeting_id/' . $meetingRecordArray['meeting_id'];

                $textTable = $this->getTextTable();

                $fullName = $meetingRecordArray['first_name'] . " " . $meetingRecordArray['middle_name'] . " " . $meetingRecordArray['last_name'];
                $message = 'Hello ' . $fullName . ', ' 
                    . "\n" . "\n";
                $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
                    . $createdByUser->getEmail() . ')'
                    . ' edited a meeting. To access the meeting go to ' 
                    . $url . '. ' .  "\n" . "\n" 
                    . 'Here is a summary of the meeting ' 
                    . "\n" . "\n";
                $contentPiece = wordwrap($contentPiece, 80, "\n", true);
                $message .= $contentPiece;
                $message .=  $textTable .  "\n"; 
                $this->sendEmail(
                    $meetingRecordArray['email'],
                    $fullName,
                    $message, 
                    $subject
                );
            }
        }
    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendDeleted($user)
    {
        $meetingRecord = $this->_meetingRecord;
        foreach($meetingRecord as $meetingRecordArray) {
            if(!empty($meetingRecordArray['email'])) {

                $subject = 'Meeting has been edited';
                $createdByUser = new Core_Model_User($meetingRecordArray['created_by']);
                $url = Core_Model_Site::getUrl();
                $url .= '/activity/meeting/viewdetails/meeting_id/' . $meetingRecordArray['meeting_id'];

                $textTable = $this->getTextTable();

                $fullName = $meetingRecordArray['first_name'] . " " . $meetingRecordArray['middle_name'] . " " . $meetingRecordArray['last_name'];
                $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' 
                    . "\n" . "\n";
                $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
                    . $createdByUser->getEmail() . ')'
                    . ' deleted a meeting. To meeting is no longer available. ' 
                    . '. ' .  "\n" . "\n" 
                    . 'Here is a summary of the meeting that was deleted' 
                    . "\n" . "\n";
                $contentPiece = wordwrap($contentPiece, 80, "\n", true);
                $message .= $contentPiece;
                $message .=  $textTable .  "\n"; 

                $this->sendEmail(
                    $meetingRecordArray['email'],
                    $fullName,
                    $message, 
                    $subject
                );
            }
        }
       
    }
}







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
class Core_Model_Activity_Call_Reminder extends Core_Model_Abstract
{
    const CALL_REMINDER_STATUS_NONE = 0;
    const CALL_REMINDER_STATUS_FIVE_MINUTE = 1;
    const CALL_REMINDER_STATUS_FIFTEEN_MINUTE = 2;
    const CALL_REMINDER_STATUS_THIRTY_MINUTE = 3;
    const CALL_REMINDER_STATUS_ONE_HOUR = 4;
    const CALL_REMINDER_STATUS_ONE_DAY = 5;
    const CALL_REMINDER_STATUS_ONE_WEEK = 6;

    /**
     * @var object call record
     */
    protected $_callRecord;

    protected $_model;

    /**
     * Execute cron
     */
    public function cron()
    {
        $this->process();
    }

    public function process()
    {
        $this->_model = new Core_Model_Activity_Call;
        $result = $this->_model->sendReminders();
        foreach ($result as $row) {
            $this->_callRecord = $row->toArray();
            $record = $this->_callRecord;
            $differenceTime = $record['end_date'] - time();
            $reminderSent = $record['reminder_sent'];
            switch ($record['reminder']){
            case self::CALL_REMINDER_STATUS_FIVE_MINUTE :
                if(($differenceTime <= 400) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::CALL_REMINDER_STATUS_FIFTEEN_MINUTE :
                if(($differenceTime >= (60*20)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::CALL_REMINDER_STATUS_THIRTY_MINUTE :
                if(($differenceTime >= (60*35)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::CALL_REMINDER_STATUS_ONE_HOUR :
                if(($differenceTime >= (60*70)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::CALL_REMINDER_STATUS_ONE_DAY :
                if(($differenceTime >= (60*60*25)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::CALL_REMINDER_STATUS_ONE_WEEK :
                if(($differenceTime >= (60*60*24*8)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::CALL_REMINDER_STATUS_NONE :
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
       $record = $this->_callRecord;
       $recepientFullName = $record['first_name'] . 
                            " " . $record['middle_name'] . 
                            " " . $record['last_name'];
       $subject = "Reminder";
       $recepient = $record['email'];

        $subject = 'Call Reminder';
        $url = Core_Model_Site::getUrl();
        $url .= '/activity/call/viewdetails/call_id/' . $record['call_id'];

        $textTable = $this->getTextTable();

        $message = 'Hello ' . $recepientFullName . ', '
            . "\n" . "\n";
        $contentPiece = ' There is a call pending by you. To access the call go to '
            . $url . '. ' .  "\n" . "\n"
            . 'Here is a summary of the call '
            . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n";
        $this->sendEmail($recepient, $recepientFullName, $message, $subject);
        $this->_model->updateReminderSent($record['call_id']);
    }

    /**
     * @return object Zend_Text_Table
     */
    public function getTextTable()
    {
        $callRecord = $this->_callRecord;

        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table(
                array('columnWidths' => array(20, 30)))
        ;
        $textTable->appendRow(
                array('Subject', $callRecord['name'])
        );
        $textTable->appendRow(
                array('Description', $callRecord['description'])
        );
        $textTable->appendRow(
                array('Assigned to', $callRecord['email'])
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

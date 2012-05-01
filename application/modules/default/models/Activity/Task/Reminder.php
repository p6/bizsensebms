<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 */

class Core_Model_Activity_Task_Reminder extends Core_Model_Abstract
{
    const TASK_REMINDER_STATUS_NONE = 0;
    const TASK_REMINDER_STATUS_FIVE_MINUTE = 1;
    const TASK_REMINDER_STATUS_FIFTEEN_MINUTE = 2;
    const TASK_REMINDER_STATUS_THIRTY_MINUTE = 3;
    const TASK_REMINDER_STATUS_ONE_HOUR = 4;
    const TASK_REMINDER_STATUS_ONE_DAY = 5;
    const TASK_REMINDER_STATUS_ONE_WEEK = 6;

    /**
     * @var object task record
     */
    protected $_taskRecord;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Activity_Task';

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
        $select->from(array('task'=>'task'))
            ->where('reminder_sent = ?', 0)
            ->join('user', 'task.assigned_to = user.user_id', 'email')
            ->join('profile', 'task.assigned_to = profile.user_id', 
                array('first_name', 'middle_name', 'last_name'));
        $result = $table->fetchAll($select);
        foreach ($result as $row) {
            $this->_taskRecord = $row->toArray();
            $record = $this->_taskRecord;
            $differenceTime = $record['end_date'] - time();
            $reminderSent = $record['reminder_sent'];
            switch ($record['reminder']){
            case self::TASK_REMINDER_STATUS_FIVE_MINUTE :
                if(($differenceTime <= 400) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::TASK_REMINDER_STATUS_FIFTEEN_MINUTE :
                if(($differenceTime >= (60*20)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::TASK_REMINDER_STATUS_THIRTY_MINUTE :
                if(($differenceTime >= (60*35)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::TASK_REMINDER_STATUS_ONE_HOUR :
                if(($differenceTime >= (60*70)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::TASK_REMINDER_STATUS_ONE_DAY :
                if(($differenceTime >= (60*60*25)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::TASK_REMINDER_STATUS_ONE_WEEK :
                if(($differenceTime >= (60*60*24*8)) and ($reminderSent == 0)) {
                    $this->composeAndSendReminder();
                }
            break;
            case self::TASK_REMINDER_STATUS_NONE :
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
        $record = $this->_taskRecord;
       $recepientFullName = $record['first_name'] . " " . 
                           $record['middle_name'] . " " . $record['last_name'];
       $subject = "Reminder";
       $recepient = $record['email'];

        $subject = 'Task Reminder';
        $url = Core_Model_Site::getUrl();
        $url .= '/activity/task/viewdetails/task_id/' . $record['task_id'];

        $textTable = $this->getTextTable();

        $message = 'Hello ' . $recepientFullName . ', '
            . "\n" . "\n";
        $contentPiece = 'There is a task pending by you. To access the task go to '
            . $url . '. ' .  "\n" . "\n"
            . 'Here is a summary of the task '
            . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n";
        $this->sendEmail($recepient, $recepientFullName, $message, $subject);
        $this->update();
    }

    /**
     * @return object Zend_Text_Table
     */
    public function getTextTable()
    {
        $taskRecord = $this->_taskRecord;

        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table(
                array('columnWidths' => array(20, 30)))
        ;
        $textTable->appendRow(
                array('Subject', $taskRecord['name'])
        );
        $textTable->appendRow(
                array('Description', $taskRecord['description'])
        );
        $textTable->appendRow(
                array('Assigned to', $taskRecord['email'])
        );
        return $textTable;
    }

    public function update()
    {
        $table = $this->getTable();
        $record = $this->_taskRecord;
        $data = array('reminder_sent'=>'1');
        $where = $table->getAdapter()->quoteInto('task_id = ?', $record['task_id']);
        $table->update($data, $where);
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

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
class Core_Model_Activity_Task_Notify_Email implements Core_Model_Observer_Interface
{
    /**
     * @var object Core_Model_Activity_Task
     */
    protected $_task;

    /**
     * @var object task record
     */
    protected $_taskRecord;

    /**
     * @var array recipients
     */
    protected $_recipients = array();    

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
                array('Status', $taskRecord['status'])
        );
        $textTable->appendRow(
                array('Start date', date('dS M Y h:i:s A', $taskRecord['start_date']))
        );
        $textTable->appendRow(
                array('End date', date('dS M Y h:i:s A', $taskRecord['end_date']))
        );
        $textTable->appendRow(
                array('Description', $taskRecord['description'])
        );
        $textTable->appendRow(
                array('Assigned to', $taskRecord['email'])
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
     * @param object $task Core_Model_Activity_Task
     */
    public function update($task)
    {  
        $this->_task = $task;
        $taskRecord = $task->fetch();
        $this->_taskRecord = $taskRecord;    
        $taskStatus = $task->getStatus();
        switch ($taskStatus) {
            case Core_Model_Activity_Task::STATUS_CREATE : 
                $this->notifyCreated();
                break;
            case Core_Model_Activity_Task::STATUS_EDIT :
                $this->notifyEdited();
                break;
            case Core_Model_Activity_Task::STATUS_DELETE :
                $this->_taskRecord = $task->getEphemeral();
                $this->notifyDeleted();
                break;
        }
    }

    /**
     * notify when task is created
     */
    public function notifyCreated()
    {
        /**
         * Notify if assignedTo if he/she != createdBy
         */
        $taskRecord = $this->_taskRecord;
        $assignedToUserId = $taskRecord['assigned_to'];
        $assignedToUser = new Core_Model_User($taskRecord['assigned_to']);    
        $currentUser = $this->getCurrentUser();
        $currentUserId = $this->getCurrentUser()->getUserId();
        if ($assignedToUserId != $currentUserId and 
            (is_numeric($assignedToUserId)) and 
            (is_numeric($currentUserId)) 
            ) {
            $currentUser = $this->getCurrentUser();
            $this->composeAndSendCreated($assignedToUser);
        
        }

       /**
        * Notify current user's  boss
        */
        $currentUserBossId = $currentUser->getProfile()->getReportsTo();
        if (is_numeric($currentUserBossId)) {
            $currentUserBoss = new Core_Model_User($currentUserBossId);
            $this->composeAndSendCreated($currentUserBoss);
        }                                 

        /**
         * Notify assiged to's boss 
         */
        $assignedToUserBossId = $assignedToUser->getProfile()->getReportsTo();
        if (is_numeric($assignedToUserBossId)) {
            $assignedToUserBoss = new Core_Model_User($assignedToUserBossId);
            $this->composeAndsendCreated($assignedToUserBoss);
        }                                                              
    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendCreated($user)
    {
        
        $taskRecord = $this->_taskRecord;
        $subject = 'New Task';
        $createdByUser = new Core_Model_User($taskRecord['created_by']);
        $url = Core_Model_Site::getUrl();
        $url .= '/activity/task/viewdetails/task_id/' . $taskRecord['task_id'];

        $textTable = $this->getTextTable();

        $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' 
            . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
            . $createdByUser->getEmail() . ')'
            . ' created a new task. To access the task go to ' 
            . $url . '. ' .  "\n" . "\n" 
            . 'Here is a summary of the task ' 
            . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
        $this->sendEmail(
            $user->getEmail(), 
            $user->getProfile()->getFullName(), 
            $message, 
            $subject
        );

    }

    /**
     * Notify when the observable is edited
     */
    public function notifyEdited()
    {
        /**
         * Notify if assignedTo if he/she != createdBy
         */
        $taskRecord = $this->_taskRecord;

        $assignedToUserId = $taskRecord['assigned_to'];
        $assignedToUser = new Core_Model_User($taskRecord['assigned_to']);    
        $currentUser = $this->getCurrentUser();
        $currentUserId = $this->getCurrentUser()->getUserId();
        if ($assignedToUserId != $currentUserId and 
            (is_numeric($assignedToUserId)) and 
            (is_numeric($currentUserId)) 
            ) {
            $currentUser = $this->getCurrentUser();
            $this->composeAndSendEdited($assignedToUser);
        
        }

       /**
        * Notify current user's  boss
        */
        $currentUserBossId = $currentUser->getProfile()->getReportsTo();
        if (is_numeric($currentUserBossId)) {
            $currentUserBoss = new Core_Model_User($currentUserBossId);
            $this->composeAndSendEdited($currentUserBoss);
        }                                 

        /**
         * Notify assiged to's boss 
         */
        $assignedToUserBossId = $assignedToUser->getProfile()->getReportsTo();
        if (is_numeric($assignedToUserBossId)) {
            $assignedToUserBoss = new Core_Model_User($assignedToUserBossId);
            $this->composeAndsendEdited($assignedToUserBoss);
        }                                                              

    }


    /**
     * @param object Core_Model_User
     */
    public function composeAndSendEdited($user)
    {
        
        $subject = 'Task has been edited';
        $taskRecord = $this->_taskRecord;
        $createdByUser = new Core_Model_User($taskRecord['created_by']);
        $url = Core_Model_Site::getUrl();
        $url .= '/activity/task/viewdetails/task_id/' . $taskRecord['task_id'];

        $textTable = $this->getTextTable();

        $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' 
            . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
            . $createdByUser->getEmail() . ')'
            . ' edited a task. To access the task go to ' 
            . $url . '. ' .  "\n" . "\n" 
            . 'Here is a summary of the task ' 
            . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
        $this->sendEmail(
            $user->getEmail(), 
            $user->getProfile()->getFullName(), 
            $message, 
            $subject
        );

    }

    /**
     * Notify when the task is deleted
     */
    public function notifyDeleted()
    {

     /**
      * Notify if assignedTo if he/she != createdBy
         */
        $taskRecord = $this->_taskRecord;

        $assignedToUserId = $taskRecord['assigned_to'];
        $assignedToUser = new Core_Model_User($taskRecord['assigned_to']);    
        $currentUser = $this->getCurrentUser();
        $currentUserId = $this->getCurrentUser()->getUserId();
        if ($assignedToUserId != $currentUserId and 
            (is_numeric($assignedToUserId)) and 
            (is_numeric($currentUserId)) 
            ) {
            $currentUser = $this->getCurrentUser();
            $this->composeAndSendDeleted($assignedToUser);
        
        }

       /**
        * Notify current user's  boss
        */
        $currentUserBossId = $currentUser->getProfile()->getReportsTo();
        if (is_numeric($currentUserBossId)) {
            $currentUserBoss = new Core_Model_User($currentUserBossId);
            $this->composeAndSendDeleted($currentUserBoss);
        }                                 

        /**
         * Notify assiged to's boss 
         */
        $assignedToUserBossId = $assignedToUser->getProfile()->getReportsTo();
        if (is_numeric($assignedToUserBossId)) {
            $assignedToUserBoss = new Core_Model_User($assignedToUserBossId);
            $this->composeAndsendDeleted($assignedToUserBoss);
        }                                                              

    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendDeleted($user)
    {
        
        $subject = 'Task has been deleted';
        $taskRecord = $this->_taskRecord;
        $createdByUser = new Core_Model_User($taskRecord['created_by']);

        $textTable = $this->getTextTable();

        $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' 
            . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
            . $createdByUser->getEmail() . ')'
            . ' deleted a task. To task is no longer available. ' 
            . '. ' .  "\n" . "\n" 
            . 'Here is a summary of the task that was deleted' 
            . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
        $this->sendEmail(
            $user->getEmail(), 
            $user->getProfile()->getFullName(), 
            $message, 
            $subject
        );

    }
}

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
class Core_Model_Account_Notes_Notify_Email 
      implements Core_Model_Observer_Interface
{
    /**
     * @var array the recipients  
     */
    protected $_recipients = array();  
    
    /**
     * @var array the note data  
     */
    protected $_notesData =  array();  

    /**
     * @return formatted text table
     */
    public function getTextTable()
    {
        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);
        $viewHelper =  new BV_View_Helper_TimestampToHuman;
        $createdOn = $viewHelper->timestampToHuman($this->_notesData['created']);
        $textTable = new Zend_Text_Table(array('columnWidths' => array(20, 55)));
        $textTable->appendRow(array('Created on', $createdOn));
        $textTable->appendRow(array('Notes', $this->_notesData['notes']));
        return $textTable;
    }
    
    /**
     * @return formatted message for email
     */
    public function getMessage($recepientName, $createdByUserFullName)
    {
        $url = Core_Model_Site::getUrl();
        $url .= '/account/notes/account_id/' . $this->_notesData['account_id'];
        $message = 'Hello ' . $recepientName . ', ' . "\n" . "\n";
        $message .=  $createdByUserFullName 
        . ' added notes to a account. ' .
        'To access the notes go to ' . "\n" 
        .  $url.'. ' .  "\n" . "\n" . 'Here is a summary of the Notes '."\n\n"
        .  "\n";
        $message .= $this->getTextTable();
        return $message;
    }
    
    /*
     * @param $recipient = email recipient
     * @param $message = email message
     * @description Sends the email and adds the recipient to sent history
     */
    public function sendEmail($recipient, $recipientFullName, $message, $subject)
    {
        $subject .= " - BizSense";
        $mail = new Core_Service_Mail;
        $mail->setBodyText($message);
        $mail->addTo($recipient, $recipientFullName);
        $mail->setSubject($subject);
        if (!(in_array($recipient, $this->_recipients))) {
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
     * send email to reported user and to whom note is assigned 
     */
    public function update($observable)
    {
        if ($observable->getStatus() !=
                                     Core_Model_Account_Notes::STATUS_CREATED) {
            return;
        }
        $this->_notesData = $observable->fetch();
        
        $subject = 'New Account Note';
        
        $userModel = new Core_Model_User($this->_notesData['created_by']);
        $userRecord = $userModel->fetch();
        $createdByUserFullName = $userModel->getProfile()->getFullName();
        $userIdOfReportedTo = $userRecord->reports_to;
          if ($userIdOfReportedTo != '') {
            $reportedUser = new Core_Model_User($userIdOfReportedTo);
            $reportedUserInfo = $reportedUser->fetch();
            $reportedUserFullName = $reportedUser->getProfile()->getFullName();
            $reportedUserEmail = $reportedUserInfo->email;
            $message = $this->getMessage($reportedUserFullName, 
                                                        $createdByUserFullName);
              
            $this->sendEmail($reportedUserEmail, $reportedUserFullName,     
                                                        $message, $subject);
        }
        
        $account = $observable->getModel();
        $accountInfo = $account->fetch();
        $assignedToUserId = $accountInfo->assigned_to;
        $user = new Core_Model_User($assignedToUserId);
        $assignedToUserInfo = $user->fetch();
        $assignedUserFullName = $user->getProfile()->getFullName();
        $assignedToUserEmail = $assignedToUserInfo->email;
        
        $message = $this->getMessage($assignedUserFullName, 
                                                        $createdByUserFullName);
                
        $this->sendEmail($assignedToUserEmail, $assignedUserFullName, 
                                                            $message, $subject);
    }
}

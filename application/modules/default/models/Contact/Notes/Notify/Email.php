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

class Core_Model_Contact_Notes_Notify_Email implements Core_Model_Observer_Interface
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
        $url .= '/contact/notes/contact_id/' . $this->_notesData['contact_id'];
        $message = 'Hello ' . $recepientName . ', ' . "\n" . "\n";
        $message .=  $createdByUserFullName 
        . ' added notes to a contact. ' .
        'To access the notes go to ' . "\n" 
        .  $url.'. ' .  "\n" . "\n" . 'The note added was: '."\n\n"
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
        if ($observable->getStatus() != Core_Model_Contact_Notes::STATUS_CREATED) {
            return;
        }
        $this->_notesData = $observable->fetch();
                        
        $subject = 'New Contact Note';
        
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
           
        $contactModel = new Core_Model_Contact($this->_notesData['contact_id']);
        $contactRecord = $contactModel->fetch();
        $assignedToUserId = $contactRecord->assigned_to;
        $userModel = new Core_Model_User($assignedToUserId);
        $assignedToUserInfo = $userModel->fetch();
        $assignedUserFullName = $userModel->getProfile()->getFullName();
        $assignedToUserEmail = $assignedToUserInfo->email;
               
        $message = $this->getMessage($assignedUserFullName, 
                            $createdByUserFullName);
        
        $this->sendEmail($assignedToUserEmail, $assignedUserFullName, $message, $subject);
        
    }
}

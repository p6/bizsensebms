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

class Core_Model_Contact_Notify_Email
{
    /**
     * @var object Core_Model_Contact
     */
    protected $_contact;

    /**
     * @var object contact record
     */
    protected $_contactRecord;

    /**
     * @var array recipients
     */
    protected $_recipients = array();    

    /**
     * @return object Zend_Text_Table
     */
    public function getTextTable()
    {
        $contactRecord = $this->_contactRecord;

        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table(
                array('columnWidths' => array(20, 30)))
        ;
        $textTable->appendRow(
                array('First Name', $contactRecord->first_name)
        );
        $textTable->appendRow(
                array('Middle Name', $contactRecord->middle_name)
        );
        $textTable->appendRow(
                array('Last Name', $contactRecord->last_name)
        );
        $textTable->appendRow(
                array('Home phone', $contactRecord->home_phone)
        );
        $textTable->appendRow(
                array('Work phone', $contactRecord->work_phone)
        );
        $textTable->appendRow(
                array('Mobile', $contactRecord->mobile)
        );
        $textTable->appendRow(
                array('Work email', $contactRecord->work_email)
        );
        $textTable->appendRow(
                array('Billing City', $contactRecord->billing_city)
        );
        $textTable->appendRow(
                array('Billing State', $contactRecord->billing_state)
        );
        $textTable->appendRow(
                array('Billing Country', $contactRecord->billing_country)
        );
        $textTable->appendRow(
                array('Description', $contactRecord->description)
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
     * @param object $contact Core_Model_Contact
     */
    public function update($contact)
    {
        $this->_contact = $contact;
        $contactRecord = $contact->fetch();
        $this->_contactRecord = $contactRecord;    
        $contactStatus = $contact->getStatus();

        switch ($contactStatus) {
            case Core_Model_Contact::STATUS_CREATE : 
                $this->notifyCreated();
                break;
            case Core_Model_Contact::STATUS_EDIT :
                $this->notifyEdited();
                break;
            case Core_Model_Contact::STATUS_DELETE :
                $this->_contactRecord = $contact->getEphemeral();
                $this->notifyDeleted();
                break;
            case Core_Model_Contact::STATUS_CONVERT :
                $this->notifyConverted();
                break;
        }
            
    }

    /**
     * notify when contact is created
     */
    public function notifyCreated()
    {
        /**
         * Notify if assignedTo if he/she != createdBy
         */
        $contactRecord = $this->_contactRecord;

        $assignedToUserId = $contactRecord->assigned_to;
        $assignedToUser = new Core_Model_User($contactRecord->assigned_to);    
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
        
        $contactRecord = $this->_contactRecord;
        $subject = 'New Contact';
        $createdByUser = new Core_Model_User($contactRecord->created_by);
        $url = Core_Model_Site::getUrl();
        $url .= '/contact/viewdetails/contact_id/' . $contactRecord->contact_id;

        $textTable = $this->getTextTable();

        $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' 
            . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
            . $createdByUser->getEmail() . ')'
            . ' created a new contact. To access the contact go to ' 
            . $url . '. ' .  "\n" . "\n" 
            . 'Here is a summary of the contact ' 
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
        $contactRecord = $this->_contactRecord;

        $assignedToUserId = $contactRecord->assigned_to;
        $assignedToUser = new Core_Model_User($contactRecord->assigned_to);    
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
        
        $subject = 'Contact has been edited';
        $contactRecord = $this->_contactRecord;
        $createdByUser = new Core_Model_User($contactRecord->created_by);
        $url = Core_Model_Site::getUrl();
        $url .= '/contact/viewdetails/contact_id/' . $contactRecord->contact_id;

        $textTable = $this->getTextTable();

        $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' 
            . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
            . $createdByUser->getEmail() . ')'
            . ' edited a contact. To access the contact go to ' 
            . $url . '. ' .  "\n" . "\n" 
            . 'Here is a summary of the contact ' 
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
     * Notify when the contact is deleted
     */
    public function notifyDeleted()
    {

     /**
      * Notify if assignedTo if he/she != createdBy
         */
        $contactRecord = $this->_contactRecord;

        $assignedToUserId = $contactRecord->assigned_to;
        $assignedToUser = new Core_Model_User($contactRecord->assigned_to);    
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
        
        $subject = 'Contact has been deleted';
        $contactRecord = $this->_contactRecord;
        $createdByUser = new Core_Model_User($contactRecord->created_by);

        $textTable = $this->getTextTable();

        $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' 
            . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() . '(' 
            . $createdByUser->getEmail() . ')'
            . ' deleted a contact. To contact is no longer available. ' 
            . '. ' .  "\n" . "\n" 
            . 'Here is a summary of the contact that was deleted' 
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

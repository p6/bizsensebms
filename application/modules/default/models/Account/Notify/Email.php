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

class Core_Model_Account_Notify_Email
{

    /**
     * @var object Core_Model_Account
     */
    protected $_account;

    /**
     * @var object account record
     */
    protected $_accountRecord;

    /**
     * @var array the recipients
     */
    protected $_recipients = array();    

    /**
     * @return object Zend_Text_Table
     */
    public function getTextTable()
    {
        $accountRecord = $this->_accountRecord;
        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table(
            array(
                'columnWidths' => array(20, 55)
            )
        );
        $textTable->appendRow(
            array(
                'Account name', $accountRecord->account_name
            )
        );
        $textTable->appendRow(
            array(
                'Phone', $accountRecord->phone
            )
        );
        $textTable->appendRow(
            array(
                'Mobile', $accountRecord->mobile
            )
        );
        $textTable->appendRow(
            array(
                'Email', $accountRecord->email
            )
        );
        $textTable->appendRow(
            array(
                'Website', $accountRecord->website
            )
        );
        $textTable->appendRow(
            array(
                'Billing City', $accountRecord->billing_city
            )
        );
        $textTable->appendRow(
            array(
                'Billing State', $accountRecord->billing_state
            )
        );
        $textTable->appendRow(
            array(
                'Billing Country', $accountRecord->billing_country
            )
        );
        $textTable->appendRow(
            array(
                'Description', $accountRecord->description
            )
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
     * Updates the notifiers
     * @param object $account
     */
    public function update($account)
    {
        $this->_account = $account;
        $accountRecord = $account->fetch();
        $this->_accountRecord = $accountRecord;    
        $accountStatus = $account->getStatus();

        switch ($accountStatus) {
            case Core_Model_Account::STATUS_CREATE : 
                $this->notifyCreated();
                break;
            case Core_Model_Account::STATUS_EDIT :
                $this->notifyEdited();
                break;
            case Core_Model_Account::STATUS_DELETE :
                $this->_accountRecord = $account->getEphemeral();
                $this->notifyDeleted();
                break;
            case Core_Model_Account::STATUS_CONVERT :
                $this->notifyConverted();
                break;
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
     * Notifies when the observable is created
     */
    public function notifyCreated()
    {
        $accountRecord = $this->_accountRecord;

        $assignedToUserId = $accountRecord->assigned_to;
    
        $currentUserId = $this->getCurrentUser()->getUserId();
        $assignedToUser = new Core_Model_User($accountRecord->assigned_to);    

        /*
         * Notify if assigned to if he/she != created by
         */
        if ($assignedToUserId != $currentUserId and 
            (is_numeric($assignedToUserId)) and 
            (is_numeric($currentUserId))
            ) {
                $this->composeAndSendCreated($assignedToUser);
        }

        /*
         * Notify current user's  boss
         */
        $currentUser = $this->getCurrentUser();
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
            $this->composeAndSendCreated($assignedToUserBoss);
         }                                                              
       
    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendCreated($user)
    {
        $accountRecord = $this->_accountRecord;
        $subject = 'New Account Has Been Created';

        $assignedTo = $accountRecord->assigned_to;
        $assignedToUser = new Core_Model_User($assignedTo);
        $createdByUser = $this->getCurrentUser();

        $url = Core_Model_Site::getUrl();

        $url .= '/account/viewdetails/account_id/' . $accountRecord->account_id;
                       
        $textTable = $this->getTextTable();
        
        $message = 'Hello ' . $assignedToUser->getProfile()->getFullName() 
            . ', ' . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() 
            . '(' . $createdByUser->getEmail() . ')'
            . ' created a new account. To access the account go to ' 
            . $url . '. ' .  "\n" . "\n" . 'Here is a summary of the account ' 
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

    /*
     * Notifies when the observable is edited
     */
    public function notifyEdited()
    {
        $accountRecord = $this->_accountRecord;

        $assignedToUserId = $accountRecord->assigned_to;
    
        $currentUserId = $this->getCurrentUser()->getUserId();
        $assignedToUser = new Core_Model_User($accountRecord->assigned_to);    

        /**
         * Notify if assigned to if he/she != created by
         */
        if ($assignedToUserId != $currentUserId and 
            (is_numeric($assignedToUserId)) and 
            (is_numeric($currentUserId))
            ) {
                $this->composeAndSendEdited($assignedToUser);
        }

        /**
         * Notify current user's  boss
         */
        $currentUser = $this->getCurrentUser();
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
            $this->composeAndSendEdited($assignedToUserBoss);
         }                                                              
      

    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendEdited($user)
    {
        $accountRecord = $this->_accountRecord;
        $subject = 'Account Has Been Edited';

        $assignedTo = $accountRecord->assigned_to;
        $assignedToUser = new Core_Model_User($assignedTo);
        $createdByUser = $this->getCurrentUser();

        $url = Core_Model_Site::getUrl();

        $url .= '/account/viewdetails/account_id/' . $accountRecord->account_id;
                       
        $textTable = $this->getTextTable();
        
        $message = 'Hello ' . $assignedToUser->getProfile()->getFullName() 
            . ', ' . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() 
            . '(' . $createdByUser->getEmail() . ')'
            . ' edited an account. To access the account go to ' 
            . $url . '. ' .  "\n" . "\n" . 'Here is a summary of the account ' 
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


    /*
     * Notifies when the observable is deleted
     */
    public function notifyDeleted()
    {
        $accountRecord = $this->_accountRecord;

        $assignedToUserId = $accountRecord->assigned_to;
    
        $currentUserId = $this->getCurrentUser()->getUserId();
        $assignedToUser = new Core_Model_User($accountRecord->assigned_to);    

        /**
         * Notify if assigned to if he/she != created by
         */
        if ($assignedToUserId != $currentUserId and 
            (is_numeric($assignedToUserId)) and 
            (is_numeric($currentUserId))
            ) {
                $this->composeAndSendDeleted($assignedToUser);
        }

        /**
         * Notify current user's  boss
         */
        $currentUser = $this->getCurrentUser();
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
            $this->composeAndSendDeleted($assignedToUserBoss);
         }                                                              
    }

    /**
     * @param object Core_Model_User
     */
    public function composeAndSendDeleted($user)
    {
        $accountRecord = $this->_accountRecord;
        $subject = 'Account Has Been Deleted';

        $assignedTo = $accountRecord->assigned_to;
        $assignedToUser = new Core_Model_User($assignedTo);
        $createdByUser = $this->getCurrentUser();

        $textTable = $this->getTextTable();
        
        $message = 'Hello ' . $assignedToUser->getProfile()->getFullName() 
            . ', ' . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() 
            . '(' . $createdByUser->getEmail() . ')'
            . ' deleted an account. The accout is no longer available. ' 
            . '. ' .  "\n" . "\n" 
            . 'Here is a summary of the account that was deleted ' 
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

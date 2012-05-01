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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Opportunity_Notify_Email
{
    /**
     * @var object the opportunity model
     */
    protected $_opportunity;

    /**
     * @var object opportunity record
     */
    protected $_opportunityRecord;

    /**
     * @var array the recipients
     */
    protected $_recipients = array();    

    /**
     * @return object Zend_Text_Table
     */
    public function getTextTable()
    {
        $opportunityRecord = $this->_opportunityRecord;
        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table
                        (array('columnWidths' => array(20, 75)));
        $textTable->appendRow(
                        array('Opportunity Name', $opportunityRecord->name));
        $textTable->appendRow(array('Amount', $opportunityRecord->amount));
        $textTable->appendRow(
                        array(
                            'Expected close date', 
                            $opportunityRecord->expected_close_date
                        )
                    );
        $textTable->appendRow(array('Lead source', $opportunityRecord->source));
        $textTable->appendRow(array('Sales stage', $opportunityRecord->stage));
        $textTable->appendRow(
                        array(
                            'Account name', 
                            $opportunityRecord->account_name
                        )
                    );
        $textTable->appendRow(
                        array(
                            'Description', 
                            $opportunityRecord->description
                        )
                    );
        return $textTable;
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
        if (!in_array($recipient, $this->_recipients)) {
            $this->_recipients[] = $recipient;
            try  {  
                $mail->send();
             }   
            catch (Zend_Exception $e) {
                $log = new Core_Service_Log;
                $info = 'Failed in connecting mail server';
                $log->info($info);
            }
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
     * @param object Core_Model_Opportunity
     */
    public function update($opportunity)
    {
        $this->_opportunity = $opportunity;
        $opportunityRecord = $opportunity->fetch();
        $this->_opportunityRecord = $opportunityRecord;    
        $opportunityStatus = $opportunity->getStatus();

        switch ($opportunityStatus) {
            case Core_Model_Opportunity::STATUS_CREATE : 
                $this->notifyCreated();
                break;
            case Core_Model_Opportunity::STATUS_EDIT :
                $this->notifyEdited();
                break;
            case Core_Model_Opportunity::STATUS_DELETE :
                $this->_opportunityRecord = 
                    $opportunity->getEphemeral();
                $this->notifyDeleted();
                break;
            case Core_Model_Opportunity::STATUS_CONVERT :
                $this->notifyConverted();
                break;
        }
           
    }


    /**
     * notify when the opportunity is created
     */
    public function notifyCreated()
    {
        /**
         * Notify if assigned to != created by
         */
        $opportunityRecord = $this->_opportunityRecord;

        $assignedToUserId = $opportunityRecord->assigned_to;
        $assignedToUser = new Core_Model_User($assignedToUserId);    
        $currentUserId = $this->getCurrentUser()->getUserId();

        if (($assignedToUserId != $currentUserId) and 
            (is_numeric($assignedToUserId)) and 
            (is_numeric($currentUserId))
            ) {

            $this->composeAndSendCreated($assignedToUser);
        } 

        /**
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
        
        $opportunityRecord = $this->_opportunityRecord;
        $url = Core_Model_Site::getUrl();
        $url .= '/opportunity/viewdetails/opportunity_id/' . 
                    $opportunityRecord->opportunity_id;
 
        $textTable = $this->getTextTable();
        $createdByUserId = $opportunityRecord->created_by;
        $createdByUser = new Core_Model_User($createdByUserId);

        $message = 'Hello ' . $user->getProfile()->getFullName() 
                    . ', ' . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() 
                        . '(' . $createdByUser->getEmail() . ')'
                        . ' created a new opportunity. ' 
                        . 'To access the opportunity go to ' 
                        . $url . '. ' .  "\n" . "\n" 
                        . 'Here is a summary of the opportunity ' 
                        . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
        $subject = 'New Opportunity';
        $this->sendEmail(
            $user->getEmail(), 
            $user->getProfile()->getFullName(), 
            $message, 
            $subject
        );

    }

    /**
     * Notifies when the observable is edited
     */
    public function notifyEdited()
    {
        /**
         * Notify if assigned to != created by
         */
        $opportunityRecord = $this->_opportunityRecord;

        $assignedToUserId = $opportunityRecord->assigned_to;
        $assignedToUser = new Core_Model_User($assignedToUserId);    
        $currentUserId = $this->getCurrentUser()->getUserId();

        if (($assignedToUserId != $currentUserId) and 
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
        
        $opportunityRecord = $this->_opportunityRecord;
        $url = Core_Model_Site::getUrl();
        $url .= '/opportunity/viewdetails/opportunity_id/' . 
                    $opportunityRecord->opportunity_id;
 
        $textTable = $this->getTextTable();
        $createdByUserId = $opportunityRecord->created_by;
        $createdByUser = new Core_Model_User($createdByUserId);

        $message = 'Hello ' . $user->getProfile()->getFullName() 
                    . ', ' . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() 
                        . '(' . $createdByUser->getEmail() . ')'
                        . ' edited an opportunity. ' 
                        . 'To access the opportunity go to ' 
                        . $url . '. ' .  "\n" . "\n" 
                        . 'Here is a summary of the opportunity ' 
                        . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
        $subject = 'An Opportunity is updated';
        $this->sendEmail(
            $user->getEmail(), 
            $user->getProfile()->getFullName(), 
            $message, 
            $subject
        );

    }


    /**
     * Notifies when the observable is deleted
     */
    public function notifyDeleted()
    {
        /**
         * Notify if assigned to != created by
         */
        $opportunityRecord = $this->_opportunityRecord;

        $assignedToUserId = $opportunityRecord->assigned_to;
        $assignedToUser = new Core_Model_User($assignedToUserId);    
        $currentUserId = $this->getCurrentUser()->getUserId();

        if (($assignedToUserId != $currentUserId) and 
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
        $opportunityRecord = $this->_opportunityRecord;
 
        $textTable = $this->getTextTable();
        $createdByUserId = $opportunityRecord->created_by;
        $createdByUser = new Core_Model_User($createdByUserId);

        $message = 'Hello ' . $user->getProfile()->getFullName() 
                    . ', ' . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() 
                        . '(' . $createdByUser->getEmail() . ')'
                        . ' deleted an opportunity. ' 
                        . 'The opportunity is no longer available ' 
                        . '. ' .  "\n" . "\n" 
                        . 'Here is a summary of the opportunity which was deleted.' 
                        . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
        $subject = 'An Opportunity has been deleted';
        $this->sendEmail(
            $user->getEmail(), 
            $user->getProfile()->getFullName(), 
            $message, 
            $subject
        );

    }



}

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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Lead_Notify_Email
{
    /**
     * @var object Core_Model_Lead
     */
    protected $_lead;

    /**
     * @var object stdClass lead record
     */
    protected $_leadRecord;

    /**
     * @var array of recipients
     */
    protected $_recipients = array();    


    /**
     * @param object stdClass lead record
     * @return object Zend_Text_Table
     */
    public function getTextTable()
    {
        $leadRecord = $this->_leadRecord;
        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table(array('columnWidths' => array(20, 30)));
        $textTable->appendRow(array('First Name', $leadRecord['first_name']));
        $textTable->appendRow(array('Middle Name', $leadRecord['middle_name']));
        $textTable->appendRow(array('Last Name', $leadRecord['last_name']));
        $textTable->appendRow(array('Company', $leadRecord['company_name']));
        $textTable->appendRow(array('Home phone', $leadRecord['home_phone']));
        $textTable->appendRow(array('Work phone', $leadRecord['work_phone']));
        $textTable->appendRow(array('Mobile', $leadRecord['mobile']));
        $textTable->appendRow(array('Email', $leadRecord['email']));
        $textTable->appendRow(array('City', $leadRecord['city']));
        $textTable->appendRow(array('State', $leadRecord['state']));
        $textTable->appendRow(array('Country', $leadRecord['country']));
        $textTable->appendRow(array('Description', $leadRecord['description']));
        return $textTable;
    }

    /**
     * Sends the email and adds the recipient to sent history
     *
     * @param $recipient = email recipient
     * @param $message = email message
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
     * @return object the lead data
     */
    public function getLeadData()
    {
        $leadRecord = $this->_leadRecord;
        return $leadRecord;
    }


    /**
     * Update the observer 
     *
     * @param object $lead the observable 
     */
    public function update($lead)
    {
        $this->_lead = $lead;
        $leadRecord = $lead->fetch();
        $this->_leadRecord = $leadRecord;    
        $leadStatus = $lead->getStatus();

        switch ($leadStatus) {
            case Core_Model_Lead::STATUS_CREATE : 
                $this->notifyCreated();
                break;
            case Core_Model_Lead::STATUS_EDIT :
                $this->notifyEdited();
                break;
            case Core_Model_Lead::STATUS_DELETE :
                $this->_leadRecord = $lead->getPreviousLeadData();
                $this->notifyDeleted();
                break;
            case Core_Model_Lead::STATUS_CONVERT :
                $this->notifyConverted();
                break;
        }
            
    }

    /**
     * Notify users about the lead created
     */
    public function notifyCreated()
    {
        $currentUser = $this->getCurrentUser();
       
        if (!$currentUser->getUserId()) {
            return;
        }

        $this->notifyCreatedAssignedTo();
        $this->notifyCreatedCurrentUserBoss();
        $this->notifyCreatedAssignedToUserBoss();

    }

    /**
     * Notify the user to whom the lead is assigned to
     */
    public function notifyCreatedAssignedTo()
    {
        $leadRecord = $this->_leadRecord;
        $assignedToUserId = $leadRecord['assigned_to'];
        $createdByUserId = $leadRecord['created_by'];

        $currentUserId = $this->getCurrentUser()->getUserId();

        /**
         * Notify if assignedTo if he/she != createdBy
         */
        if (
            ($assignedToUserId !== $createdByUserId) and 
            ($assignedToUserId != null) and 
            ($createdByUserId != null)
            ) {
            $assignedToUser = new Core_Model_User($leadRecord['assigned_to']);    
            $assignedToUserFullName = $assignedToUser->getProfile()
                                        ->getFullName();
            $createdByUser = new Core_Model_User($leadRecord['created_by']);
                
            $createdByUserFullName = $createdByUser->getProfile()
                                        ->getFullName(); 
            $createdByUserEmail = $createdByUser->getEmail();

                 
            $message = $this->getCreatedMessage($assignedToUser);

            $subject = $this->getCreatedSubject();
            $this->sendEmail(
                $assignedToUser->getEmail(), $assignedToUserFullName, 
                $message, $subject
            );
        }


    }

    /**
     * Notify current user's  boss
     */
    public function notifyCreatedCurrentUserBoss() 
    {
        $leadRecord = $this->_leadRecord;
        $assignedToUser = new Core_Model_User($leadRecord['assigned_to']);    
        $currentUser = $this->getCurrentUser();

        if (!$currentUser->getUserId()) {
            return;
        }

        $currentUserBossId = $currentUser->getProfile()->getReportsTo();

        if (!$currentUserBossId) {
            return;
        }

        $currentUserBoss = new Core_Model_User($currentUserBossId);

        $message = $this->getCreatedMessage($currentUserBoss);
        $this->sendEmail($currentUserBoss->getEmail(), 
            $currentUserBoss->getProfile()->getFullName(), 
            $message, $this->getCreatedSubject()
        );
   
    }
  
    /**
     * Notify assigedTo's boss 
     */
    public function notifyCreatedAssignedToUserBoss()
    {
        $leadRecord = $this->_leadRecord;

        if (!is_numeric($leadRecord['assigned_to'])) {
            return;
        }

        $assignedToUser = new Core_Model_User($leadRecord['assigned_to']);    
        $assignedToUserProfileData = $assignedToUser->getProfile()->fetch();
        $assignedToUserBossId = $assignedToUser->getProfile()->getReportsTo();
        if (!is_numeric($assignedToUserBossId)) {
            return;
        }

        $assignedToUserBoss = new Core_Model_User($assignedToUserBossId);
        $assignedToUserBossFullName = $assignedToUserBoss->getProfile()->getFullName();
        $message = $this->getCreatedMessage($assignedToUserBoss);
        $this->sendEmail($assignedToUserBoss->getEmail(), $assignedToUserBossFullName, 
                $message, $this->getCreatedSubject());
    }                                                              



    /**
     * Notify users when the lead is edited
     */
    public function notifyEdited()
    {

        $leadRecord = $this->_leadRecord;
        $subject = 'Lead has been edited';

        $assignedToUserId = $leadRecord['assigned_to'];
        $createdByUserId = $leadRecord['created_by'];

        /*
         * Notify if assigned to and created by are different users
         */
        $currentUserId = $this->getCurrentUser()->getUserId();

        if (
            ($assignedToUserId != $createdByUserId) and 
            (is_numeric($assignedToUserId)) and 
            (is_numeric($createdByUserId))
            ) {

            $assignedToUser = new Core_Model_User($leadRecord['assigned_to']);    
            $createdByUser = new Core_Model_User($leadRecord['created_by']);
           
            $createdByUserFullName = $createdByUser->getProfile()->getFullName();
            $createdByUserEmail = $createdByUser->getEmail();
            $message = $this->getEditedMessage($createdByUser);     
            $this->sendEmail($assignedToUser->getEmail(), 
                $assignedToUser->getProfile()->getFullName(), 
                $message, $this->getEditedSubject());
        }


        /**
         * Notify current user's  boss
         */
        $currentUser = $this->getCurrentUser();
        $currentUserBossId = $currentUser->getProfile()->getReportsTo();
        if (is_numeric($currentUserBossId)) {
            $currentUserBoss = new Core_Model_User($currentUserBossId);
            $currentUserBossFullName = $currentUserBoss->getProfile()
                                            ->getFullName();
            $message = $this->getEditedMessage($currentUserBoss);
            $this->sendEmail($currentUserBoss->getEmail(), 
                $currentUserBossFullName, $message, 
                $this->getEditedSubject()
            );
        }                                 

        /**
         * Notify assiged to's boss 
         */
        $assignedToUserId = $this->_leadRecord['assigned_to'];
        if (!is_numeric($assignedToUserId)) {
            return;
        } else {
            $assignedToUser = new Core_Model_User($assignedToUserId);
        }
        $assignedToUserBossId = $assignedToUser->getProfile()->getReportsTo();
        if (is_numeric($assignedToUserBossId)) {
            $assignedToUserBoss = new Core_Model_User($assignedToUserBossId);
            $assignedToUserBossFullName = $assignedToUserBoss->getProfile()
                ->getFullName();
            $message = $this->getEditedMessage($assignedToUserBoss);
            $this->sendEmail($assignedToUserBoss->getEmail(), 
                    $assignedToUserBossFullName, $message, 
                    $this->getEditedSubject()
            );
        }                                                              
    }


    /**
     * Notify watchers about lead status
     */
    public function notifyDeleted()
    {
        $leadRecord = $this->_lead->getEphemeral();
        $this->_leadRecord = $leadRecord;
        $subject = 'Lead has been deleted';

        $assignedToUserId = $leadRecord['assigned_to'];
        $createdByUserId = $leadRecord['created_by'];

        /**
         * Notify if assigned to and current user are different users
         */
        $currentUser = $this->getCurrentUser();
        $currentUserId = $currentUser->getUserId();


        if (($assignedToUserId != $currentUserId) and 
            (is_numeric($assignedToUserId)) 
            ) {
            $assignedToUser = new Core_Model_User($leadRecord['assigned_to']);    
            $assignedToUserFullName = $assignedToUser
                ->getProfile()
                ->getFullName();
               
            $message = $this->getDeletedMessage($assignedToUser);
            $this->sendEmail($assignedToUser->getEmail(), 
                $assignedToUserFullName, $message, $subject);
        }


        /**
         * Notify current user's  boss
         */
        $currentUserBossId = $currentUser->getProfile()->getReportsTo();
        if (is_numeric($currentUserBossId)) {
            $currentUserBoss = new Core_Model_User($currentUserBossId);
            $currentUserBossFullName = $currentUserBoss->getProfile()->getFullName();

            $message = $this->getDeletedMessage($currentUserBoss);

            $this->sendEmail($currentUserBoss->getEmail(), $currentUserBossFullName, $message, $subject);
        }                                 

        /*
         * Notify assigedTo's boss 
         */
        $assignedToUser = new Core_Model_User($leadRecord['assigned_to']);
        $assignedToUserBossId = $assignedToUser->getProfile()->getReportsTo();
        if (is_numeric($assignedToUserBossId)) {
            $assignedToUserBoss = new Core_Model_User($assignedToUserBossId);
            $assignedToUserBossFullName = $assignedToUserBoss->getProfile()->getFullName();
            $message = $this->getDeletedMessage($assignedToUserBoss);
            $this->sendEmail($assignedToUserBoss->getEmail(), $assignedToUserBossFullName, $message, $subject);
        }                                                              
    }



    /**
     * @param object $user Core_Model_User
     * @return string message
     */
    public function getCreatedMessage($user)
    {
        $textTable = $this->getTextTable();
        $leadRecord = $this->_leadRecord;
        $createdByUserId = $leadRecord['created_by'];
        $createdByUser = new Core_Model_User($createdByUserId);
        $url = Core_Model_Site::getUrl();
        $url .= '/lead/viewdetails/lead_id/' . $leadRecord['lead_id'];

        $message = 'Hello ' . $user->getProfile()->getFullName();
        $message .= ', ' . "\n" . "\n";
        $contentPiece =  $createdByUser->getProfile()->getFullName() . '(';
        $contentPiece .= $createdByUser->getEmail() . ')';
        $contentPiece .= ' created a lead. To access the lead go to ';
        $contentPiece .= $url . '. ' .  "\n" . "\n";
        $contentPiece .= 'Here is a summary of the lead ' . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
      
        return $message;
    }

    /**
     * @param object $user Core_Model_User
     * @return string message
     */
    public function getEditedMessage($user)
    {
        $leadRecord = $this->_leadRecord;
        $url = Core_Model_Site::getUrl();
        $url .= '/lead/viewdetails/lead_id/' . $leadRecord['lead_id'];
        $textTable = $this->getTextTable();
        $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' . "\n" . "\n";
        $currentUser = $this->getCurrentUser();
        $contentPiece =  $currentUser->getProfile()->getFullName() . '(' . $currentUser->getEmail() . ')'
            . ' edited a lead. To access the lead go to ' 
            . $url . '. ' 
            . "\n" . "\n" 
            . 'Here is a summary of the lead which is edited.' . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
        return $message; 
    }

    /**
     * @param object $user Core_Model_User
     * @return string message 
     */
    public function getDeletedMessage($user)
    {
        $leadRecord = $this->_leadRecord;
        $textTable = $this->getTextTable();
        $message = 'Hello ' . $user->getProfile()->getFullName() . ', ' . "\n" . "\n";
        $currentUser = $this->getCurrentUser();
        $contentPiece =  $currentUser->getProfile()->getFullName() . '(' . $currentUser->getEmail() . ')'
            . ' deleted a lead. The lead is no longer available. ' 
            . "\n" . "\n" 
            . 'Here is a summary of the lead which was deleted.' . "\n" . "\n";
        $contentPiece = wordwrap($contentPiece, 80, "\n", true);
        $message .= $contentPiece;
        $message .=  $textTable .  "\n"; 
        return $message; 

    }

    /**
     * @return string the email subject when lead is created
     */
    public function getCreatedSubject()
    {
        return 'New Lead Has Been Created';
    }

    /**
     * @return string the email subject when lead is edited
     */
    public function getEditedSubject()
    {
        return 'A Lead Has Been Edited';
    }

}

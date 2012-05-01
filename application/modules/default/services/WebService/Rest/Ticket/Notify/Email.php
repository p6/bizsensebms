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
class Core_Service_WebService_Rest_Ticket_Notify_Email implements Core_Model_Observer_Interface
{
    /**
     * @var object Core_Service_Ticket
     */
    protected $_service;

    /**
     * @var array of recipients
     */
    protected $_recipients = array();    

    /**
     * @see Core_Model_Observer_Interface::update
     */
    public function update($observable)
    {
        $this->_service = $observable;
        $status = $this->_service->getStatus();
        switch ($status) {
            case Core_Service_Ticket::STATUS_CREATE :
               $this->notifyCreated(); 
            break;
            
            case Core_Service_Ticket::STATUS_EDITSTATUS:
               $this->notifyStatus(); 
            break;
            default :
            break;
        }
    }


    /**
     * @param array ticket record
     * @return object Zend_Text_Table
     */
    public function getTextTable()
    {
        $ticketRecord = $this->_service->fetch();
        if (is_numeric($ticketRecord['assigned_to'])) {
            $assignedToUser = new Core_Model_User($ticketRecord['assigned_to']);
            $email = $assignedToUser->getEmail();
            $ticketRecord['assigned_to_email'] = $email;

            $fullName = $assignedToUser->getProfile()->getFullName();
            $ticketRecord['assigned_to_name'] = $fullName;

            $viewHelper = new BV_View_Helper_TimestampToHuman();
            $ticketRecord['created_formatted_date'] = $viewHelper->timestampToHuman($ticketRecord['created']);
            
            $ticketStatus = new Core_Model_Ticket_Status();
            $ticketStatus->setTicketStatusId($ticketRecord['ticket_status_id']);
            $ticketStatusRecord = $ticketStatus->fetch();

            $ticketRecord['status'] = $ticketStatusRecord['name'];

            $contactModel = new Core_Model_Contact($ticketRecord['contact_id']);
            $ticketRecord['contact'] = $contactModel->getFullName();
            $ticketRecord['contact_email'] = $contactModel->getWorkEmail();
        }

        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table(array('columnWidths' => array(20, 30)));
        $textTable->appendRow(array('Title', $ticketRecord['title']));
        $textTable->appendRow(array('Contact', $ticketRecord['contact']));
        $textTable->appendRow(array('Contact Email', $ticketRecord['contact_email']));
        $textTable->appendRow(array('Created on', $ticketRecord['created_formatted_date']));
        $textTable->appendRow(array('Status', $ticketRecord['status']));
        $textTable->appendRow(array('Assigned To', $ticketRecord['assigned_to_name']));
        $textTable->appendRow(array('Assigned To Email', $ticketRecord['assigned_to_email']));
        $textTable->appendRow(array('Description', $ticketRecord['description']));
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
        $mail = new BV_Mail;
        $mail->setBodyText($message);
        $mail->addTo($recipient, $recipientFullName);
        $mail->setSubject($subject);
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



    public function notifyCreated()
    {
        $ticketRecord = $this->_service->fetch();

        if (!is_numeric($ticketRecord['assigned_to'])) {
            return;
        }

        $assignedToUser = new Core_Model_User($ticketRecord['assigned_to']);
        $email = $assignedToUser->getEmail();
        $fullName = $assignedToUser->getProfile()->getFullName();

        $message = "Hello " . $fullName . ",\n\n";
        $message .= "A ticket has been created. The ticket details are as below:" . "\n\n";
        $message .= $this->getTextTable();
        $message .= "\n\n";
        $this->sendEmail($email, $fullName, $message, 'Ticket has been created');
    }
    
    public function notifyStatus()
    {
        $ticketRecord = $this->_service->fetch();

        if (!is_numeric($ticketRecord['assigned_to'])) {
            return;
        }

        $assignedToUser = new Core_Model_User($ticketRecord['assigned_to']);
        $email = $assignedToUser->getEmail();
        $fullName = $assignedToUser->getProfile()->getFullName();

        $message = "Hello " . $fullName . ",\n\n";
        $message .= "A ticket status has been changed. The ticket details are as below:" . "\n\n";
        $message .= $this->getTextTable();
        $message .= "\n\n";
        $this->sendEmail($email, $fullName, $message, 'Ticket status has been changed');
    }
}

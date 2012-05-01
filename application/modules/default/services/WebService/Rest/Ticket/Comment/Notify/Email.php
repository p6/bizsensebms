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

Interface::update
     */
    public function update($observable)
    {
        $this->_service = $observable->getService();
        $this->_ticketCommentService = $observable;

        $status = $observable->getStatus();
        switch ($status) {
            case Core_Service_Ticket_Comment::STATUS_CREATE :
               $this->notifyCreated(); 
            break;
            default :
            break;
        }
    }


    /**
     * @param array ticket record
     * @return object Zend_Text_Table
     */
    public function getTicketTextTable()
    {
        $ticketRecord = $this->_service->fetch();

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
     * 
     */
    public function getCommentTextTable()
    {
        $record = $this->_ticketCommentService->fetch();
        $charset = 'ascii';
        $charset = 'ISO-8859-1';

        Zend_Text_Table::setOutputCharset($charset);

        $textTable = new Zend_Text_Table(array('columnWidths' => array(20, 30)));
        $textTable->appendRow(array('Title', $record['title']));
        $textTable->appendRow(array('Description', $record['description']));
        $viewHelper = new BV_View_Helper_TimestampToHuman();
        $date = $viewHelper->timestampToHuman($record['created']);
        $textTable->appendRow(array('Created on', $date));
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


    /**
     * Notify the watchers when the ticket comment is created
     * @return void
     */
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
        $message .= "A comment was added to the ticket. The ticket and comment details are as below:" . "\n\n";
        $message .= "Latest Comment:\n\n";
        $message .= $this->getCommentTextTable();
        $message .= "\n\n";
        $message .= "Ticket Details:\n\n";
        $message .= $this->getTicketTextTable();
        $message .= "\n\n";
        $this->sendEmail($email, $fullName, $message, 'Comment has been added to ticket');
    }
    

}

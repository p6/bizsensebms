<?php
 /*
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_User_Notify_Email_NewAccount extends BV_Model_Essential_Abstract
{
    
    /*
     * The user object
     */
    protected $_user;

    /*
     * The user data
     */
    protected $_userData;    

    /*
     * The user's password administrator typed in the form
     */
    protected $_password;
    /*
     * Receive the observable object
     */
    public function update($user, $password)
    {
        $this->_user = $user;
        $this->_userData = $user->fetch();   
        $this->_password = $password; 
        $this->sendEmail();
    }

    /*
     * Send email to user notyfing about the new account
     */
    public function sendEmail()
    {
        $recipient = $this->_userData->email;
        $recipientFullName = trim($this->_userData->first_name) . " " . trim($this->_userData->middle_name) . " " . 
            trim($this->_userData->last_name);
        $subject = "Your account details - BizSense";
        $mail = new Core_Service_Mail;
        $message = "Hello $recipientFullName," . "\n" . "\n" .  
            "An administrator created an account for you. You can access the application at " .  Core_Model_Site::getUrl();
        $message .= "\n" . 
        "The username for your account is " . $recipient." or ". $this->_userData->username
        ."\n".
        "The password for your account is " . $this->_password;
        $mail->setBodyText($message);
        $mail->addTo($recipient, $recipientFullName);
        $mail->setSubject($subject);
        $mail->send();
 
    }
}


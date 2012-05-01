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


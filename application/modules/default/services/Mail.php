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

interface
     */
    public function setFrom(
        $fromEmail = 'user@example.com', $fromName = 'BizSense CRM And ERP')
    {
        $varibaleModel = new Core_Model_Variable('email');
        $fromEmail = $varibaleModel->getValue();
        if (!$fromEmail) {
            $fromEmail = 'bizsense@' . $_SERVER['SERVER_NAME'];
        }
        $varibaleModel = new Core_Model_Variable('from_name');
        $fromName = $varibaleModel->getValue();
        if (strlen($fromName) < 2) {
            $fromName = 'BizSense Site Mailer';
        }
        
        if (null !== $this->_from) {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('From Header set twice');
        }

        $email = $this->_filterEmail($fromEmail);
        $name  = $this->_filterName($fromName);
        $this->_from = $fromEmail;
        $this->_storeHeader('From', $this->_formatAddress($fromEmail, $fromName), true);
        
        return $this;
    }

 
    /**
     * Set email server transport - SMTP or sendmail
     * @TODO fetch the record from Mail model
     */
    public function setTransport()
    {
        $varibaleModel = new Core_Model_Variable;
        $varibaleModel->setVariable('transport');
        $transport = $varibaleModel->getValue();
        if ($transport == "SMTP") {
            $varibaleModel->setVariable('smtp_server'); 
            $smtpServer = $varibaleModel->getValue();
            $varibaleModel->setVariable('smtp_require_auth'); 
            $requireAuth = $varibaleModel->getValue();
            $varibaleModel->setVariable('smtp_secure_connection'); 
            $secureConnection = $varibaleModel->getValue();

            if ($requireAuth) {
                $varibaleModel->setVariable('smtp_auth'); 
                $auth = $varibaleModel->getValue();
                
                $varibaleModel->setVariable('smtp_username');
                $smtpUsername = $varibaleModel->getValue();
                
                $varibaleModel->setVariable('smtp_password');
                $smtpPassword = $varibaleModel->getValue();
                
                $varibaleModel->setVariable('smtp_port');
                $smtpPort = $varibaleModel->getValue();
                
                $config = array(
                    'auth' => $auth,
                    'username' => $smtpUsername,
                    'password' => $smtpPassword,
                    'port'     => $smtpPort,
                );
                if($secureConnection != 'no') {
                    $config['ssl'] = $secureConnection;
                }
                $tr = new Zend_Mail_Transport_Smtp($smtpServer, $config);
            } else {
                $tr = new Zend_Mail_Transport_Smtp($smtpServer);
            
            }  
            $this->transport = $tr; 

            $this->setDefaultTransport($tr);
        }
    }    

    /**
     * Send the email
     * @return bool
     */
    public function send($transport = null)
    {
        if ($transport === null) {
            if (! self::$_defaultTransport instanceof Zend_Mail_Transport_Abstract) {
                require_once 'Zend/Mail/Transport/Sendmail.php';
                $transport = new Zend_Mail_Transport_Sendmail();
            } else {
                $transport = self::$_defaultTransport;
            }
        }

        if ($this->_date === null) {
            $this->setDate();
        }

        if(null === $this->_from && null !== self::getDefaultFrom()) {
            $this->setFromToDefaultFrom();
        }

        if(null === $this->_replyTo && null !== self::getDefaultReplyTo()) {
            $this->setReplyToFromDefault();
        }

        $transport->send($this);

        return $this;
    }
}

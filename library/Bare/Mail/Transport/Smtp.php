<?php
/**
 * Copyright (c) 2010, Binary Vibes Information Technologies Pvt. Ltd. 
 * (http://binaryvibes.co.in) All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 * 
 * * Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * 
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * * Neither the names of Binary Vibes Information Technologies Pvt. Ltd. 
 *   nor the names of the project contributors may be used to endorse or 
 *   promote products derived from this software without specific prior 
 *   written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF 
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */
class Bare_Mail_Transport_Smtp extends Zend_Mail_Transport_Smtp
{

    /**
     * Send an email via the SMTP connection protocol
     *
     * The connection via the protocol adapter is made just-in-time to allow a
     * developer to add a custom adapter if required before mail is sent.
     *
     * @return void
     */
    public function _sendMail()
    {
        // If sending multiple messages per session use existing adapter
        if (!($this->_connection instanceof Zend_Mail_Protocol_Smtp)) {
            // Check if authentication is required and determine required class
            $connectionClass = 'Bare_Mail_Protocol_Smtp';
            if ($this->_auth) {
                $connectionClass .= '_Auth_' . ucwords($this->_auth);
            }
            if (!class_exists($connectionClass)) {
                // require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($connectionClass);
            }
            $this->setConnection(new $connectionClass($this->_host, $this->_port, $this->_config));
            $this->_connection->connect();
            $this->_connection->helo($this->_name);
        } else {
            // Reset connection to ensure reliable transaction
            $this->_connection->rset();
        }
        // Set sender email address
        $this->_connection->mail($this->_mail->getFrom());

        // Set recipient forward paths
        foreach ($this->_mail->getRecipients() as $recipient) {
            $this->_connection->rcpt($recipient);
        }

        // Issue DATA command to client
        $this->_connection->data($this->header . Zend_Mime::LINEEND . $this->body);
    }

}

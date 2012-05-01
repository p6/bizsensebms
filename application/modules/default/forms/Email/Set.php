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


class Core_Form_Email_Set extends Zend_Form
{

    public function init()
    {
        $db = Zend_Registry::get('db');
        $this->setAction('/admin/email/set')
                ->setMethod('post');
        
        $email = $this->createElement('text', 'email')
                        ->setLabel('Site email')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_EmailAddress());

        $fromName = $this->createElement('text', 'from_name')
                        ->setLabel('From Name')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(0, 100));


        $transport = $this->createElement('select', 'transport')
                        ->setLabel('Transport');
        $transport->addMultiOptions(array('SMTP'=>'SMTP'));    
        $transport->addMultiOptions(array('sendmail'=>'sendmail')); 
    
        $smtpServer = $this->createElement('text', 'smtp_server')    
                            ->setLabel('SMTP Server Name');

        $smtpRequireAuth = $this->createElement('checkbox', 'smtp_require_auth')
                                    ->setLabel('Server reuires authentication');
            
        $smtpAuth = $this->createElement('select', 'smtp_auth')
                        ->setLabel('SMTP Authentication type');
        $smtpAuth->addMultiOptions(array('plain'=>'PLAIN')); 
        $smtpAuth->addMultiOptions(array('login'=>'LOGIN')); 
        $smtpAuth->addMultiOptions(array('crammd5'=>'CRAM-MD5')); 


        $smtpUsername = $this->createElement('text', 'smtp_username')    
                            ->setLabel('SMTP Username');
        $smtpPassword = $this->createElement('password', 'smtp_password')    
                            ->setLabel('SMTP Password');

        $smtpSecureConnection = $this->createElement('select', 'smtp_secure_connection')
                                    ->setLabel('SMTP Secure Connection');

        $smtpSecureConnection->addMultiOptions(array('no'=>'No')); 
        $smtpSecureConnection->addMultiOptions(array('tls'=>'TLS')); 
        $smtpSecureConnection->addMultiOptions(array('ssl'=>'SSL')); 
   
        $smtpPort = $this->createElement('text', 'smtp_port')
                            ->setValue('25')
                            ->setAttrib('size', '2')
                            ->setLabel('SMTP Port'); 
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');

        $footer = $this->createElement('textarea', 'footer')
                        ->setLabel('Email Footer')  
                        ->setDescription('This text will be appended to all outgoing emails')
                        ->setAttribs(array('cols'=>'40', 'rows'=>'5'));   
 
        $this->setElementFilters(array('StringTrim'));
        $this->addElements(array($email, $fromName, $transport, $smtpServer, 
            $smtpAuth, $smtpRequireAuth, $smtpUsername, 
            $smtpPassword, $smtpSecureConnection, $smtpPort, $footer, $submit));

        /*
         * Populate the form
         */
        $result = array();
        $variableModel = new Core_Model_Variable;
        
        $variableModel->setVariable('email');
        $result['email'] = $variableModel->getValue();
        
        $variableModel->setVariable('from_name');
        $result['from_name'] = $variableModel->getValue();
        
        $variableModel->setVariable('transport');
        $result['transport'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_server');
        $result['smtp_server'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_require_auth');
        $result['smtp_require_auth'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_username');
        $result['smtp_username'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_secure_connection');
        $result['smtp_secure_connection'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_port');
        $result['smtp_port'] = $variableModel->getValue();
        
        $variableModel->setVariable('footer');
        $result['footer'] = $variableModel->getValue();
        
        
        if ($result) {
            $this->populate($result); 
        }
    }
}



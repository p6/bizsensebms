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
class Core_Form_Newsletter_Queue_Settings extends Zend_Form
{
    public function init() 
    {
        $maximumMailsPerHour = 
            $this->createElement('text', 
                'newsletter_message_queue_settings_maximum_mails_per_hour')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(1,50))
                        ->setLabel('Maximium mails per hour');

        $delayBetweenTwoMails = 
            $this->createElement('text', 
            'newsletter_message_queue_settings_delay_between_two_mails')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(1,50))
                        ->setDescription('In seconds. Usually mail servers add delay bewteen mails. If your mail server does not, supply the delay value.')
                        
                        ->setLabel('Delay between two mails');

        $numberOfMailsPerDomainPerHour = 
            $this->createElement('text', 
        'newsletter_message_queue_settings_number_of_mails_per_domain_per_hour')
                        ->setRequired(true)
                        ->setDescription('100 mails per domain per hour is recommended')
                        ->addValidator(new Zend_Validate_StringLength(1,50))
                        ->setLabel('Number of mails per domain per hour'); 
        
        $fromEmail = $this->createElement('text', 
                'newsletter_message_queue_settings_from_email')
                    ->setLabel('From email address')
                    ->setRequired(true)
                    ->addValidator(new Zend_Validate_StringLength(0, 320))
                    ->addValidator(new Zend_Validate_EmailAddress());

        $useVerp = $this->createElement('checkbox',
            'newsletter_message_queue_settings_use_verp')
                    ->setDescription('If you do not use VERP, BizSense will not handle bounces automatically')
                     ->addValidator(new Core_Model_Newsletter_Message_Queue_Validate_VerpFields)
                    ->setLabel('Use VERP');

        $fromEmailName = $this->createElement('text',
            'newsletter_message_queue_settings_from_email_name')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(1,50))
                        ->setLabel('From email name');

        $smtpServer = $this->createElement('text', 
                'newsletter_message_queue_settings_from_email_smtp_server')    
                            ->setDescription('Setting IP address solves DNS lookup problems')
                            ->setLabel('SMTP server name');

        $smtpRequireAuth = $this->createElement('checkbox', 
            'newsletter_message_queue_settings_from_email_smtp_require_auth')
                        ->setLabel('Server requires authentication');

        $smtpAuthType = $this->createElement('select', 
            'newsletter_message_queue_settings_from_email_smtp_auth_type')
                        ->setDescription('Currently only PLAIN is supported')
                        ->setLabel('SMTP authentication type');
        $smtpAuthType->addMultiOptions(array('plain'=>'PLAIN')); 
        $smtpAuthType->addMultiOptions(array('login'=>'LOGIN')); 
        $smtpAuthType->addMultiOptions(array('crammd5'=>'CRAM-MD5')); 

        $smtpUsername = $this->createElement('text', 
            'newsletter_message_queue_settings_from_email_smtp_username')    
                            ->setLabel('SMTP username');

        $smtpPassword = $this->createElement('password', 
            'newsletter_message_queue_settings_from_email_smtp_password')    
                            ->setLabel('SMTP password');

        $replayToemail = $this->createElement('text', 
                'newsletter_message_queue_settings_reply_to_email')
                    ->setLabel('Reply to email address')
                    ->setRequired(true)
                    ->addValidator(new Zend_Validate_StringLength(0, 320))
                    ->addValidator(new Zend_Validate_EmailAddress());


        $replayToEmailName = $this->createElement('text',
            'newsletter_message_queue_settings_reply_to_email_name')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(1,50))
                        ->setLabel('Reply to email name');

        $returnPath = $this->createElement('text', 'newsletter_message_queue_settings_bounce_return_path')
                        ->setDescription('When a message is bounced, the mail server sends a message to the VERP email address. Mention the value of the Return-Path header of this email. On some mail servers the default is MAILER-DAEMON')
                        ->setLabel('Bounce message Return-Path header value');
        $fromHeader = $this->createElement('text', 'newsletter_message_queue_settings_bounce_from')
                        ->setDescription('When a message is bounced, the mail server sends a message to the VERP email address. Mention the value of the from header of this email. On some mail servers the default is MAILER-DAEMON@<hostname>')
                        ->setLabel('Bounce message from header value');
                        
        $bounceCallBackUrl = $this->createElement('text', 'newsletter_message_queue_settings_bounce_callback_url')
                                ->setDescription('When an email is automatically unsubscribed by the bounce handler BizSense can make an HTTP request to the URL you specify here. Applications implenting the BizSense REST client can update themselves about the email being unsubscribed. The value \'&email=userbeingunsubscribed@example.com\' will be appended to the URL')
                        ->addValidator(new BV_Validate_Uri)
                        ->setLabel('Bounce process call back URL');
                                
        $thresholdBounceMessage = $this->createElement('text',
                     'newsletter_message_queue_settings_threshold_bounce_message')
                        ->setLabel('Bounce threshold');
                                
        $bounceTimeSettings = $this->createElement('text', 
            'newsletter_message_queue_settings_bounce_time_settings')    
                            ->setLabel('Bounce handling interval')
                            ->setRequired(true)
                            ->setDescription('Format: hour: minute: seconds');
                            
        $forwardBounceEmailsTo = $this->createElement('text', 
            'newsletter_message_queue_forward_bounce_emails_to')    
                            ->setLabel('Forward bounced emails to')
                            ->setDescription('Emails which are bounced will be
                                  forward to this email id before deleting');
                            

        $submit = $this->createElement('submit', 'submit')
                        ->setIgnore(true)
                       ->setAttrib('class', 'submit_button');

        $this->setElementFilters(array('StringTrim'));
        $this->addElements(array($maximumMailsPerHour, $delayBetweenTwoMails, 
                        $numberOfMailsPerDomainPerHour, $fromEmail, $useVerp,
                        $fromEmailName, $smtpServer, $smtpRequireAuth, 
                        $smtpAuthType, $smtpUsername, $smtpPassword,                                        
                        $replayToemail, $replayToEmailName, $returnPath, 
                        $fromHeader, $bounceCallBackUrl,$bounceTimeSettings,
                        $forwardBounceEmailsTo,$thresholdBounceMessage, $submit)); 
    }
}    

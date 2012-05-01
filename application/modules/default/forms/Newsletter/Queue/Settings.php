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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/**
 * Create newsletter queue create form
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

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

class Core_Model_Newsletter_Message_Queue extends Core_Model_Abstract
{

    const MESSAGE_NOT_SENT = 0;
    const MESSAGE_SENT = 1;

    const MAILS_PER_HOUR = 'newsletter_message_queue_settings_maximum_mails_per_hour';
    const DELAY_BETWEEN_MAILS = 'newsletter_message_queue_settings_delay_between_two_mails';
    const MAILS_PER_DOMAIN_PER_HOUR =  'newsletter_message_queue_settings_number_of_mails_per_domain_per_hour';
    const FROM_EMAIL = 'newsletter_message_queue_settings_from_email';
    const USE_VERP = 'newsletter_message_queue_settings_use_verp';
    const FROM_EMAIL_NAME = 'newsletter_message_queue_settings_from_email_name';
    const SMTP_SERVER = 'newsletter_message_queue_settings_from_email_smtp_server';
    const REQUIRE_AUTH = 'newsletter_message_queue_settings_from_email_smtp_require_auth';
    const SMTP_AUTH_TYPE = 'newsletter_message_queue_settings_from_email_smtp_auth_type';
    const SMTP_USERNAME = 'newsletter_message_queue_settings_from_email_smtp_username';
    const SMTP_PASSWORD = 'newsletter_message_queue_settings_from_email_smtp_password';
    const REPLY_TO_EMAIL =  'newsletter_message_queue_settings_reply_to_email';
    const REPLY_TO_EMAIL_NAME =  'newsletter_message_queue_settings_reply_to_email_name';
    const BOUNCE_RETURN_PATH =  'newsletter_message_queue_settings_bounce_return_path';
    const BOUNCE_FROM = 'newsletter_message_queue_settings_bounce_from';
    const CALLBACK_URL = 'newsletter_message_queue_settings_bounce_callback_url';
    const BOUNCE_LAST_PROCESSED = 'newsletter_message_queue_settings_bounce_last_processed';
    const THRESHOLD_BOUNCE_MESSAGE = 'newsletter_message_queue_settings_threshold_bounce_message';
    const BOUCE_TIME_SETTINGS ='newsletter_message_queue_settings_bounce_time_settings';
    const FORWARD_BOUNCE_EMAILS_TO = 'newsletter_message_queue_forward_bounce_emails_to';

    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Newsletter_MessageQueue';
    
    /**
     * @var object Core_Model_Variable
     */
    protected $_variableModel;

    /**
     * @var object Core_Service_Mail
     */
    protected $_mailService;

    /**
     * @var object Core_Service_Logger
     */
    protected $_loggerService;

    /**
     * @var object Zend_Mail_Transport_Abstract
     */
    protected $_mailTransport;

    /**
     * @var array the list of forbidden domains for the hour
     */
    protected $_hourlyForbiddenList = array();

    /**
     * @var int batch counter
     */
    protected $_batchCounter = 0;

    /**
     * @var the message queue id
     */
     protected $_messageQueueId;
    
    /**
     * @param int messageQueueId
     * @return fluent interface
     */
    public function setMessageQueueId($messageQueueId)
    {
        $this->_messageQueueId = $messageQueueId;
        return $this;
    }

    /**
     * @return int the message queue id
     */
    public function getMessageQueueId()
    {
        return $this->_messageQueueId;
    }
    
    /**
     * Initialize the controller
     */
    public function getVariableModel()
    {
        if ($this->_variableModel === null) {
            $this->_variableModel = new Core_Model_Variable;
        }
        return $this->_variableModel;
    }

    /**
     * @param array $data
     * @return int the queue id
     */
    public function create($data)
    {
        $table = $this->getTable();
        return $table->insert($data);
    }
    
    /**
     * Updates the row in the message queue table
     * @param array $data
     * @return int 
     */ 
    public function edit($data = array()) 
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('message_queue_id = ?', $this->_messageQueueId);
        $result = $table->update($data, $where);
        return $result;
    }    

    /**
     * Execute cron
     */
    public function cron()
    {
        $this->processQueue();
        $this->processBounce();
    }

    /**
     * @param array $data
     * @return fluent interface
     */
    public function saveSettings($data)
    {
        foreach ($data as $key=>$value) {
            $this->getVariableModel()->save($key, $value);
        }
        return $this;
    }
    
    /**
     * @return array settings record
     * @TODO performance return settings in memory, if not load from cache, 
     * if not load from the database
     */
    public function fetchSettings()
    {
        $variableModel =  $this->getVariableModel();
        $mailQueueSettings = array();
        $mailQueueSettingsKeys = array(
            self::MAILS_PER_HOUR,
            self::DELAY_BETWEEN_MAILS,
            self::MAILS_PER_DOMAIN_PER_HOUR,
            self::FROM_EMAIL,
            self::USE_VERP,
            self::FROM_EMAIL_NAME,
            self::SMTP_SERVER,
            self::REQUIRE_AUTH,
            self::SMTP_AUTH_TYPE,
            self::SMTP_USERNAME,
            self::SMTP_PASSWORD,
            self::REPLY_TO_EMAIL,
            self::REPLY_TO_EMAIL_NAME,
            self::BOUNCE_RETURN_PATH,
            self::BOUNCE_FROM,
            self::CALLBACK_URL,
            self::BOUNCE_LAST_PROCESSED,
            self::BOUCE_TIME_SETTINGS,
            self::FORWARD_BOUNCE_EMAILS_TO,
            self::THRESHOLD_BOUNCE_MESSAGE
        );
        foreach($mailQueueSettingsKeys as $value ) {
            $variableModel->setVariable($value); 
            $mailQueueSettings[$value] = $variableModel->getValue();
        }
        return $mailQueueSettings;
    }

    /**
     * Process the queue
     */
    public function processQueue()
    {
        $this->_queueInit();
       
        while ($this->_isQueuePending()) {
            foreach ($this->_getTargetList() as $row) {
                $rowArray = $row->toArray();
                $domain = $rowArray['domain'];
                if ($this->_canSendToDomain($domain)) { 
                        $this->_send($rowArray);
                } else {
                    $this->_addToHourlyForbiddenList($domain);
                }
            }
        }

        $this->_queueClose();

    }

    /**
     * Is the queue pending?
     * @return bool
     */
    protected function _isQueuePending()
    {
        $batchDetails = $this->_getBatchDetails();
        $batchMaximumMails = $batchDetails['maximum_mails_allowed_in_current_batch'];
        $maximumMailsPerHour = $batchDetails['maximum_mails_per_hour'];
        $numberOfMailsSentInLastHour = $batchDetails['number_of_mails_sent_in_last_hour'];
        $totalPendingInQueue = $batchDetails['number_of_mails_in_queue'];
        
        $this->generalLog("number of emails sent in last one hour " . $numberOfMailsSentInLastHour);
        $this->generalLog("maximum number of allowed emails per hour " . $maximumMailsPerHour); 
        $this->generalLog("mails pending in queue " . $totalPendingInQueue); 
        
        if ($totalPendingInQueue <= 0) {
            $this->generalLog("can no longer send messages in this batch"); 
            return false;
        }
        if ($batchMaximumMails > 0) {
            return true;
        } else {
            $this->generalLog("can no longer send messages in this batch"); 
            return false;
        }

    }

    /**
     * @return array batch details
     */
    protected function _getBatchDetails()
    {
        $table = $this->getTable();     
        $db = $table->getAdapter();

        $mailQueueSettings = $this->fetchSettings(); 
        $maximunMailsPerHour =  $mailQueueSettings[self::MAILS_PER_HOUR];

        $date = new Zend_Date();
        $currentTimestamp = $date->getTimestamp();
        $date->sub(1, Zend_Date::HOUR);
        $timestampBeforeCurrentHour = $date->getTimestamp();
        $maximumMailsPerHour = $mailQueueSettings[self::MAILS_PER_HOUR]; 
    
        /**
         * @TODO performance
         * Perhaps, we could add a limit 1 clause to this statement
         */         
        $table = $this->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        
        $select->from(array('mq'=>'message_queue'), array('count(*) as count'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id');
        $select->where('mq.status = ?', self::MESSAGE_SENT);
        $select->where("sent_time between '$timestampBeforeCurrentHour' and '$currentTimestamp'"); 
        $result = $table->fetchRow($select);
        if ( $result) {
           $result = $result->toArray();
           $numberOfMailsSentInLastHour = $result['count'];
        } else {
          $numberOfMailsSentInLastHour = 0;
        }
        $batchMaximumMails = $maximumMailsPerHour - $numberOfMailsSentInLastHour;
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $select->from(array('mq'=>'message_queue'), array('count(*) as count'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id',array());
       $select->where('mq.status = ?', self::MESSAGE_NOT_SENT);
       if (!empty($this->_hourlyForbiddenList)) {
            $expression = "s.domain NOT IN (";
            foreach ($this->_hourlyForbiddenList as $forbiddenDomain) {
                $expression .= "'" . $forbiddenDomain . "'" . ",";
            }
            $expression = rtrim($expression, ",");
            $expression .= ")";
            $select->where(new Zend_Db_Expr($expression));
        }
        $domainBlacklistModel = new Core_Model_Newsletter_DomainBlacklist;
        $blacklistDomains = $domainBlacklistModel->fetchAll();
        $blackesListedDomains = "";
        
        if(!empty($blacklistDomains)) {
            for($i = 0; $i <= sizeof($blacklistDomains)-1; $i += 1) { 
                $blackesListedDomains .= "'".$blacklistDomains[$i]['domain']. "',";
            }
            $blackesListedDomains = rtrim($blackesListedDomains, ",");
            $exp = "s.domain NOT IN ($blackesListedDomains)";
            $select->where(new Zend_Db_Expr($exp));
        } 
        $select->where('s.status = ?', Core_Model_Newsletter_Subscriber::ACTIVE); 
        $result = $table->fetchRow($select);
        if ( $result) {
           $result = $result->toArray();
        }        
        $totalToBeSent = $result['count'];
        $batchDetails = array(
            'number_of_mails_sent_in_last_hour' => $numberOfMailsSentInLastHour,
            'maximum_mails_per_hour' => $maximunMailsPerHour,
            'maximum_mails_allowed_in_current_batch' => $batchMaximumMails,
            'number_of_mails_in_queue' => $totalToBeSent,
        );
    
        return $batchDetails;
    }

    protected function _getTargetList()
    {
        $batchDetails = $this->_getBatchDetails();
        $sentInBatch = $this->_batchCounter;
        $canSend = $batchDetails['maximum_mails_allowed_in_current_batch'] - $sentInBatch;
                
        /**
         *@TODO performance
         * add where domain not in forbidden list
         */
        $table = $this->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        
        $select->from(array('mq'=>'message_queue'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id',array(
                'mq.subscriber_id'=>'s.subscriber_id as subscriber_id',
                'first_name', 'middle_name', 'last_name','email', 'format', 'domain'));
        $select->where('mq.status = ?', self::MESSAGE_NOT_SENT);
        
        if (!empty($this->_hourlyForbiddenList)) {
            $expression = "s.domain NOT IN (";
            foreach ($this->_hourlyForbiddenList as $forbiddenDomain) {
                $expression .= "'" . $forbiddenDomain . "'" . ",";
            }
            $expression = rtrim($expression, ",");
            $expression .= ")";
            $select->where(new Zend_Db_Expr($expression));
        }
        
        $domainBlacklistModel = new Core_Model_Newsletter_DomainBlacklist;
        $blacklistDomains = $domainBlacklistModel->fetchAll();
        $blackesListedDomains = "";
        
        if(!empty($blacklistDomains)) {
            for($i = 0; $i <= sizeof($blacklistDomains)-1; $i += 1) { 
                $blackesListedDomains .= "'".$blacklistDomains[$i]['domain']. "',";
            }
            $blackesListedDomains = rtrim($blackesListedDomains, ",");
            $exp = "s.domain NOT IN ($blackesListedDomains)";
            $select->where(new Zend_Db_Expr($exp));
        } 
        
        if ($canSend > 500) {
            $canSend = 500;
        }
        $select->limit($canSend);
        
        $result = $table->fetchAll($select);
        
        if ($result) {
            return $result;
        } else{
            $this->generalLog("no messages left in queue");
            return array();
        }
        
    }

    /**
     * The queue is initialized. Make log entries
     */
    protected function _queueInit()
    {
        $batchDetails = $this->_getBatchDetails();
        $batchMaximumMails = $batchDetails['maximum_mails_allowed_in_current_batch']; 
        $this->generalLog('initialized queue process');
        $this->generalLog("can send $batchMaximumMails in this batch"); 
    }

    /**
     * The queue processing is closed. Make log entries
     */
    protected function _queueClose()
    {
        $this->generalLog("sent " . $this->_batchCounter . " messages in this batch");
        $this->generalLog("completed queue process");
    }

    /**
     * @param string $domain
     * @return bool
     */
    protected function _canSendToDomain($domain)
    {
        $db = $this->getTable()->getAdapter();
        if (in_array($domain, $this->_hourlyForbiddenList)) {
            $this->generalLog($domain . " is in hourly forbidden list");
            return false;
        }
        $date = new Zend_Date();
        $currentTimestamp = $date->getTimestamp();
        $date->sub(1, Zend_Date::HOUR);
        $timestampBeforeCurrentHour = $date->getTimestamp();

        $mailQueueSettings = $this->fetchSettings();
        $maximumMailsPerDomainPerHour = $mailQueueSettings[self::MAILS_PER_DOMAIN_PER_HOUR];
        
        $table = $this->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $select->from(array('mq'=>'message_queue'), array('count(*) as count'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id',array());
        $select->where('s.status = ?', Core_Model_Newsletter_Subscriber::ACTIVE);
        $select->where('s.domain = ?', $domain);
        $select->where("sent_time between '$timestampBeforeCurrentHour' and '$currentTimestamp'");             
        
        if (!empty($this->_hourlyForbiddenList)) {
            $expression = "s.domain NOT IN (";
            foreach ($this->_hourlyForbiddenList as $forbiddenDomain) {
                $expression .= "'" . $forbiddenDomain . "'" . ",";
            }
            $expression = rtrim($expression, ",");
            $expression .= ")";
            $select->where(new Zend_Db_Expr($expression));
        }
        
        $domainBlacklistModel = new Core_Model_Newsletter_DomainBlacklist;
        $blacklistDomains = $domainBlacklistModel->fetchAll();
        $blackesListedDomains = "";
        
        if(!empty($blacklistDomains)) {
            for($i = 0; $i <= sizeof($blacklistDomains)-1; $i += 1) { 
                $blackesListedDomains .= "'".$blacklistDomains[$i]['domain']. "',";
            }
            $blackesListedDomains = rtrim($blackesListedDomains, ",");
            $exp = "s.domain NOT IN ($blackesListedDomains)";
            $select->where(new Zend_Db_Expr($exp));
        }
        
        $result = $table->fetchRow($select);
        if ( $result) {
           $result = $result->toArray();
           $mailsSentToDomainInLastHour = $result['count'];
        } else {
           $mailsSentToDomainInLastHour = 0;
        }        
        $domainDifference = $maximumMailsPerDomainPerHour - $mailsSentToDomainInLastHour;
        
        if ($domainDifference) {
            return true;
        } else{
            $this->generalLog("adding domain " . $domain . " to hourly forbidden list");
            return false;
        }

    }
    
    /**
     * @param array $record
     */
    protected function _send($record)
    {
        $logger = $this->getGeneralLoggerService();
        $date = new Zend_Date();
        $currentTimestamp = $date->getTimestamp();
        
        $subscriberId = $record['subscriber_id'];
        $subscriberModel = new Core_Model_Newsletter_Subscriber;
        $subscriberDetails = $subscriberModel->setSubscriberId($subscriberId)
                                             ->fetch();       
        $email = $subscriberDetails['email'];
        
        $messageQueueId = $record['message_queue_id'];
    
        $randomNumber = rand();
        
        /**
         * @TODO stronger salt is required
         */
        $hashToGenarate = $currentTimestamp . $messageQueueId . $randomNumber;
        $hash = sha1($hashToGenarate);
        
        $table = $this->getTable();
        $db = $table->getAdapter();
        $dataToUpdate = array(
            'hash' => $hash,
        );
        $where = $db->quoteInto('message_queue_id = ?', $record['message_queue_id']);
        $table->update($dataToUpdate, $where);

        /**
         * @TODO performance
         * We could fetch the self service URL only once per queue
         */
        $webService = new Core_Model_WebService();
        $clientUrl = $webService->getSelfServiceUrl();
        $unsubscribeLink = $clientUrl . '/newsletter/unsubscribe/hash/' . $hash;

        $messageContent = $this->_getMessageContent($record, $unsubscribeLink);
        try {
            $mailService = $this->getMailService();
            $mailService->clearRecipients();
            $mailService->clearSubject();
            $subscriberId = $record['subscriber_id'];
            $subsciberModel = new Core_Model_Newsletter_Subscriber;
            $subsciberRecord = $subsciberModel->setSubscriberId($subscriberId)
                                          ->fetch();
            $mailService->addTo($subsciberRecord['email']);
            $mailService->setSubject($messageContent['subject']);
            $mailService->setBodyText($messageContent['text']);
            if ($record['format'] == Core_Model_Newsletter_Subscriber::FORMAT_HTML) {
                $mailService->setBodyHtml($messageContent['html']);
            }
            $mailService->send();
            $this->generalLog('sent message to ' . $subsciberRecord['email']);
            $this->_batchCounter += 1;
            $this->generalLog("current batch count is " . $this->_batchCounter);
            $dataToUpdate = array(
                'status' => self::MESSAGE_SENT,   
                'sent_time' => time(),
            );
            $where = $db->quoteInto('message_queue_id = ?', $record['message_queue_id']);
            $table->update($dataToUpdate, $where);
        } catch (Exception $exception) {
            $this->generalLog("check your newsletter settings");
            $this->generalLog("exception" . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param array $record
     * @param string $unsubscribeLink
     * @return array
     */
    protected function _getMessageContent($record, $unsubscribeLink)
    {   
        if (isset($record['unique_message'])) {
            return $this->_getCustomMessageContent($record);    
        }
        $subscriberId = $record['subscriber_id'];
        $subsciberModel = new Core_Model_Newsletter_Subscriber;
        $subsciberRecord = $subsciberModel->setSubscriberId($subscriberId)
                                          ->fetch();
        $firstName = $subsciberRecord['first_name'];
        $middleName = $subsciberRecord['middle_name'];
        $lastName = $subsciberRecord['last_name'];

        $messageModel = new Core_Model_Newsletter_Message();
        $messageModel->setMessageId($record['message_id']);

        $messageText = $messageModel->getMessageText();
        $messageHtml = $messageModel->getMessageHtml();

        $messageRecord = $messageModel->fetch();
        $tokens = array('@first_name', '@middle_name', '@last_name', '@unsubscribe_url');
        $replacements = array($firstName, $middleName, $lastName, $unsubscribeLink);

        $parsedTextMessage = str_replace(
            $tokens, $replacements, $messageText
        );

        $parsedHtmlMessage = str_replace(
            $tokens, $replacements, $messageHtml
        );

        $toReturn = array(
            'subject' => $messageRecord['subject'],
            'text' => $parsedTextMessage,
            'html' => $parsedHtmlMessage,
        );
        return $toReturn;
    }
   
    /**
     * @param array $record
     * @return array
     */
    protected function _getCustomMessageContent($record)
    {
        $toReturn = array(
            'subject' => $record['custom_subject'],
            'text' => $record['custom_text_body'],
            'html' => $record['custom_html_body'],
        );
        return $toReturn;
    }
   
    /**
     * @param string $domain
     * @return fluent interface
     */
    protected function _addToHourlyForbiddenList($domain)
    {
        if (!in_array($domain, $this->_hourlyForbiddenList)) {
            $this->_hourlyForbiddenList[] = $domain; 
        }
        return $this;
    }

    /**
     * @return object Core_Service_Mail
     */
    public function getMailService()
    {
        if ($this->_mailService == null) {
            $transport = $this->getMailTransport();
            Zend_Mail::setDefaultTransport($transport);
            $this->_mailService = new Zend_Mail;
            $settings = $this->fetchSettings();
            $fromEmail = $settings[self::FROM_EMAIL];
            $fromName = $settings[self::FROM_EMAIL_NAME];
            $replyTo = $settings[self::REPLY_TO_EMAIL];
            $replyToName = $settings[self::REPLY_TO_EMAIL_NAME];
            $this->_mailService->setReplyTo($replyTo, $replyToName);
            $this->_mailService->setFrom($fromEmail, $fromName);
            $this->_mailService->addHeader('Precedence', 'bulk');
        }
        return $this->_mailService;
    }

    /**
     * @return object mail transport
     */
    public function getMailTransport()
    {
        if ($this->_mailTransport == null) {
            $settings = $this->fetchSettings();
            $hostname = $settings[self::SMTP_SERVER];
            if ($settings[self::USE_VERP]) {
                $this->getVerpMailTransport();          
            } else {
                $this->getNonVerpMailTransport();
            }
        }
        return $this->_mailTransport;
    }

    /**
     * @return object Bare_Mail_Transport_Smtp
     */
    public function getVerpMailTransport()
    {
        $settings = $this->fetchSettings();
        $hostname = $settings[self::SMTP_SERVER];
        if ($settings[self::REQUIRE_AUTH]) {
            $config = array(
                'auth' => $settings[self::SMTP_AUTH_TYPE],
                'username' => $settings[self::SMTP_USERNAME],
                'password' => $settings[self::SMTP_PASSWORD],
            );
            $this->_mailTransport = new Bare_Mail_Transport_Smtp($hostname, $config);
        } else {
            $this->_mailTransport = new Bare_Mail_Transport_Smtp($hostname);
        }
        return $this->_mailTransport;
    }

    /**
     * @return object Bare_Mail_Transport_Smtp
     */
    public function getNonVerpMailTransport()
    {
        $settings = $this->fetchSettings();
        $hostname = $settings[self::SMTP_SERVER];
        if ($settings[self::REQUIRE_AUTH]) {
            $config = array(
                'auth' => $settings[self::SMTP_AUTH_TYPE],
                'username' => $settings[self::SMTP_USERNAME],
                'password' => $settings[self::SMTP_PASSWORD],
            );
            $this->_mailTransport = new Zend_Mail_Transport_Smtp($hostname, $config);
        } else {
            $this->_mailTransport = new Zend_Mail_Transport_Smtp($hostname);
        }
        return $this->_mailTransport;
    }


    /**
     * @return object Core_Service_Logger
     */
    public function getLoggerService()
    {
        if ($this->_loggerService == null) {
            $this->_loggerService = new Core_Service_Log;
        }
        return $this->_loggerService;
    }


    /**
     * @param string $hash
     * @return mixed array|null the queue record
     */
    public function fetchByHash($hash)
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('hash = ?', $hash);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
        
    }

    /**
     * Automated bounce processing
     */
    public function processBounce()
    {
        $settings = $this->fetchSettings();
        $lastProcessed = $settings[self::BOUNCE_LAST_PROCESSED];
        
        if (is_numeric($lastProcessed)) {
            $lastProcessedDate = new Zend_Date($lastProcessed);
            $bounceTimeSetting = $settings[self::BOUCE_TIME_SETTINGS];
            $bounceTime = explode(":", $bounceTimeSetting);
            $bounceTimeCheck = count($bounceTime);
            $date = new Zend_Date;
            if ($bounceTimeCheck = 3) {
                $hour = $bounceTime['0'];
                $date->sub($hour, Zend_Date::HOUR);
                $minutes = $bounceTime['1'];
                $date->sub($minutes, Zend_Date::MINUTE);
                $seconds = $bounceTime['2'];
                $date->sub($seconds, Zend_Date::SECOND);
            }                
            if ( ($date->getTimestamp() - $lastProcessed) >= 1) {
                //Run the process    
            } else {
                $dateHelper = new BV_View_Helper_TimestampToHuman;
                $this->generalLog("Bounce last processed " . $dateHelper->timestampToHuman($lastProcessed));
                $this->generalLog("Skipping bounce process");
                return;
            }
        }
        
        $verp = $settings[self::USE_VERP];
        if (!$verp) {
            return;
        }
        $this->generalLog("bounce processing initialized");
        try {
           $this->_processBounce(); 
        } catch (Exception $e) {
            $this->generalLog("An error occured while fetching message: " . $e->getMessage());
        }
        $this->saveSettings(array(self::BOUNCE_LAST_PROCESSED=>time()));
        $this->generalLog("bounce processing completed");
    }

    protected function _processBounce()
    {
        $settings = $this->fetchSettings();
        $hostname = $settings[self::SMTP_SERVER];
        $username = $settings[self::SMTP_USERNAME];
        $password = $settings[self::SMTP_PASSWORD];
        $returnPathSettings = $settings[self::BOUNCE_RETURN_PATH];
        $bounceFromSettings = $settings[self::BOUNCE_FROM];
        
        $mail = new Zend_Mail_Storage_Pop3(
                    array(
                        'host' => $hostname,
                        'user' => $username,
                        'password' => $password
                    )
                );
        
        $totalMessages = count($mail);
        $this->generalLog("There are $totalMessages messages in mailbox");      
        foreach ($mail as $messageNumber=>$message) {            
            $headers = $message->getHeaders();
           
            if (!isset($headers['return-path'])) {
                $message = $mail->getMessage($messageNumber);
                $this->redirectBounceMail($message, $headers);
                $mail->removeMessage($messageNumber);
                continue;
            }
            $returnPath = $headers['return-path'];
            if ($returnPathSettings != $returnPath) {
                $message = $mail->getMessage($messageNumber);
                $this->redirectBounceMail($message, $headers);
                $mail->removeMessage($messageNumber);
                continue;
            }

            if (!isset($headers['from'])) {
                $message = $mail->getMessage($messageNumber);
                $this->redirectBounceMail($message, $headers);
                $mail->removeMessage($messageNumber);
                continue;
            }
            $bounceFrom = $headers['from'];
            if ($bounceFromSettings != $bounceFrom) {
                $message = $mail->getMessage($messageNumber);
                $this->redirectBounceMail($message, $headers);
                $mail->removeMessage($messageNumber);
                continue;
            }

            if (!isset($headers['to'])) {
                $message = $mail->getMessage($messageNumber);
                $this->redirectBounceMail($message, $headers);
                $mail->removeMessage($messageNumber);
                continue;
            }
            $to = $headers['to'];
            if (strstr($to, '+')) {
                $message = $mail->getMessage($messageNumber);
                $this->redirectBounceMail($message, $headers);
                $mail->removeMessage($messageNumber);
                $email = $this->getEmailFromString($to);
                $this->handleBounce($email);
            } 
            $mail->removeMessage($messageNumber);
        } 
    }

    /**
     * Handle the bounce for the email
     * Unsubscribe the email from lists
     * Criteria - mail attempted to send in the last 7 days
     * Notifies the client application by making an HTTP request 
     * To the call back URL specified in settings
     * @param $email
     */
    public function handleBounce($email)
    {            
        $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $table = $this->getTable();
        $startDate = new Zend_Date();
        $startDate->sub(7, Zend_Date::DAY);
        
        $subscriberModel = new Core_Model_Newsletter_Subscriber;
        $subscriberRecord = $subscriberModel->fetchAllByEmail($email);
        $table = $this->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        
        $timestamp = $startDate->getTimestamp();
        $time = time();
        
        $select->from(array('mq'=>'message_queue'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id',array(
                'mq.subscriber_id'=>'s.subscriber_id as subscriber_id',
                'first_name', 'middle_name', 'last_name', 'email', 'format'));
        $select->where('mq.status = ?', self::MESSAGE_SENT);
        $select->where('mq.subscriber_id = ?', $subscriberRecord['subscriber_id']);
        $select->where("sent_time between '$timestamp' and '$time'"); 
        $result = $table->fetchAll($select);
       
        foreach ($result as $row) {
            $record = $row->toArray();
            $listId = $record['list_id'];
            if($subscriberRecord['bounce_count']) {
                $settings = $this->fetchSettings();
                if ($subscriberRecord['bounce_count'] >= $settings[self::THRESHOLD_BOUNCE_MESSAGE]) {
                    $unsubscribed = $subscriberModel->blockByEmailAndListId($email, $listId);
                    $this->generalLog('auto bounce handle ' . $email . ' removed from list id ' . $listId);
                }
                else {
                    $dataToUpdate['bounce_count'] = $subscriberRecord['bounce_count'] + 1;
                    $subscriberModel->setSubscriberId($subscriberRecord['subscriber_id']);
                    $subscriberModel->edit($dataToUpdate);
                }
            }
            else {
                $dataToUpdate['bounce_count'] = 1;
                $subscriberModel->setSubscriberId($subscriberRecord['subscriber_id']);
                $subscriberModel->edit($dataToUpdate);
            }
            // changing message status to Bounced
            $dataToUpdateMessageQueue = array(   
                'sent_time' => time(),
            );
            $where = $table->getAdapter()->quoteInto('message_queue_id = ?', $record['message_queue_id']);
            $table->update($dataToUpdateMessageQueue, $where);
        }
        $settings = $this->fetchSettings();
        $uri = $settings[self::CALLBACK_URL];
        if (!$uri) {
            return;
        }
        $uri .= "&email=" . urlencode($email);
        $success = file_get_contents($uri);
        if ($success) {
           $this->generalLog("Successfully made the HTTP call back after unsubscribing $email from mailing lists"); 
        } else {
           $this->generalLog("Could not make the HTTP call back after unsubscribing $email from mailing lists. Check the callback settings"); 
        }
         
    }

    /**
     * @param string $string the value of To | Delivered-To header
     * @return string email address
     */
    public function getEmailFromString($string)
    {
        $pattern = '/\+([^+]+)@/';
        preg_match($pattern, $string, $match);
        $target = $match[1];
        $pieces = explode("=", $target);
        $email = $pieces[0] . '@' . $pieces[1];
        return $email; 
    }

    /**
     * @param string $email
     * @param int $listId
     * @param int $messageId
     * @return bool
     */
    public function checkQueueDuplicate($subscriberId, $listId, $messageId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('subscriber_id = ?', $subscriberId)
                        ->where('list_id =?', $listId)
                        ->where('message_id = ?', $messageId);
        $result = $table->fetchRow($select);   
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int number of messages unsent
     */
    public function fetchTotalToBeSent()
    {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $query = "select count(*) from message_queue where status = 0";
        $result = $db->fetchOne($query);
        return $result;
    }
    
    /**
     * @return int number of messages sent
     */
    public function fetchTotalSent()
    {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $query = "select count(*) from message_queue where status = 1";
        $result = $db->fetchOne($query);
        return $result;
    }

    /**
     * @return int number of messages with html format
     */
    public function fetchByHtmlFormat()
    {
        $totalHtmlFormat =  $this->fetchTotalToBeSent() - $this->fetchByTextFormat();
        return $totalHtmlFormat;
    }
   
    /**
     * @return int number of messages with html format
     */
    public function fetchByTextFormat()
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $select->from(array('mq'=>'message_queue'),array('count(*) as count'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id',array())
            ->distinct('domain');
        $select->where('mq.status = ?', self::MESSAGE_NOT_SENT);
        $select->where('s.format = ?', Core_Model_Newsletter_Subscriber::FORMAT_TEXT);
        $result = $table->fetchRow($select);
        
        if ( $result) {
           $result = $result->toArray();
           return $result['count'];
        } else {
            return 0;
        }   
    }

    /**
     *@return int number of unique domains
     */
    public function fetchNumberOfUniqueDomains()
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $select->from(array('mq'=>'message_queue'), array('count(*) as count'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id',array('domain'))
            ->distinct('domain');
        $select->where('mq.status = ?', self::MESSAGE_NOT_SENT);
        $result = $table->fetchRow($select);
        if ( $result) {
           $result = $result->toArray();
           return $result['count'];
        } else {
            return 0;
        }   
    }

    /**
     * Log a message to the general log
     * @param string $message
     */
    public function generalLog($message)
    {
        $logger = $this->getGeneralLoggerService();
        $logger->info("Newsletter Message Queue - " . $message); 
    }

    /**
     * delete all unsent messages
     */
    public function cancelAllPendingMessages()
    {   
        $table = $this->getTable();
        $db = $table->getAdapter();
        $query = "DELETE FROM message_queue WHERE status=".self::MESSAGE_NOT_SENT;
        $result = $db->query($query);
        return $result; 
    }

    /**
     * delete  messages
     */
    public function cancelMessage($messageId)
    {   
        $table = $this->getTable();
        $db = $table->getAdapter();
        $query = $db->quoteInto("DELETE FROM message_queue WHERE message_id = ?", $messageId);
        $result = $db->query($query);
        return $result; 
    }

    /**
     * @param string message content
     * @param strin message header
     */
    public function redirectBounceMail($messageContent, $header) 
    {
        $transport = $this->getNonVerpMailTransport();
        $mailService = new Zend_Mail;
        $settings = $this->fetchSettings();
        $fromEmail = $settings[self::FROM_EMAIL];
        $fromName = $settings[self::FROM_EMAIL_NAME];
        $replyTo = $settings[self::REPLY_TO_EMAIL];
        $replyToName = $settings[self::REPLY_TO_EMAIL_NAME];
        $mailService->setReplyTo($replyTo, $replyToName);
        // attaching header contents as file
        $mailService->setFrom($fromEmail, $fromName);
        $attachBody = "";
        foreach($header as $key=>$value)  {
            // arranging data in order to print in file
            $attachBody .= "$key = $value
";
        }
        if ($settings[self::FORWARD_BOUNCE_EMAILS_TO]) {
            $mailService->addTo($settings[self::FORWARD_BOUNCE_EMAILS_TO]);
        }    
        $type = 'application/text';
        $disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        $encoding = Zend_Mime::ENCODING_BASE64;
        $filename = 'headers';
        $mailService->createAttachment($attachBody, $type, $disposition
                                                 , $encoding, $filename);
        $mailService->setSubject('Bounced Email');
        $mailService->setBodyText($messageContent);
        $mailService->send($transport);
    }
    
    /**
     * @return @Zend_Pagintor object
     */
    public function getReportPaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_Report';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }
    
    /**
     * @return @Zend_Pagintor object
     */
    public function getQueueDomainPaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_QueueDomains';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }
    
    /**
     * @return @Zend_Pagintor object
     */
    public function getGraphReportPaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_Graph';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }
    
    /**
     *
     */
     public function getNumenerOfEmailsPerDomain($domainName)
     {
        $table = $this->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $select->from(array('mq'=>'message_queue'), array('count(*) as count'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id',array('domain'))
            ->distinct('domain');
        $select->where('mq.status = ?', self::MESSAGE_NOT_SENT);
        $select->where("s.domain = ?", $domainName);
        $result = $table->fetchRow($select);
        if ( $result) {
           $result = $result->toArray();
           return $result['count'];
        } else {
            return 0;
        }   
     }
}
    
    

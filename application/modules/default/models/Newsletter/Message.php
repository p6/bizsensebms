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

class Core_Model_Newsletter_Message extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::$_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Newsletter_Message';

    /**
     * @var int message ID
     */
    protected $_messageId;
    
    /**
     * @param int $messageId
     * @return fluent interface
     */
    public function setMessageId($messageId)
    {
        $this->_messageId = $messageId;
        return $this;
    }

    /**
     * @return array
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()->where('message_id = ?', $this->_messageId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return string message text
     */
    public function getMessageText()
    {
        $record = $this->fetch();
        return $record['text'];
    }

    /**
     * @return string message text
     */
    public function getMessageHtml()
    {
        $record = $this->fetch();
        return $record['html'];
    }


    /**
     * @param array $data 
     * @return int the message ID
     */
    public function create($data = array())
    {
        $table = $this->getTable();    
        if (isset($data['campaign_id']) and !is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        if (is_numeric($data['created_by']) and $data['created_by'] < 1) {
            $data['created_by'] = null;
        }
        $data['created'] =  time();
        $messageId = $table->insert($data);             
        $this->_messageId = $messageId;        
        return $this->_messageId;
    }

    /**
     * @return number of rows updated
     * @param array $data
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        if (isset($data['campaign_id']) and !is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }
        $where = $table->getAdapter()->quoteInto('message_id = ?', 
            $this->_messageId);
        $result = $table->update($data, $where);
        return $result;
    }


    /**
     * @return int the number of messages deleted
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('message_id = ?', $this->_messageId);
        $result = $table->delete($where);
        return $result;
    }

    /**
     * @param string $recipient
     * @param string $firstName
     * @param string $middleName
     * @param string $middleName
     * @return bool
     */
    public function sendTestMessage(
        $recipient, $firstName, $middleName, $lastName)
    {
        $queueModel = new Core_Model_Newsletter_Message_Queue;
        $validator = new Zend_Validate_EmailAddress();
        if (!$validator->isValid($recipient)) {
            return false;
        }

        $record = $this->fetch();
        $subject = $record['subject'];

        $textMessage = $record['text'];
        $htmlMessage = $record['html'];

        $tokens = array('@first_name', '@middle_name', '@last_name', '@unsubscribe_url');
        $unsubscribeUrl = 'http://example.com/unsubscribe';
        $replacements = array($firstName, $middleName, $lastName, $unsubscribeUrl);
        $parsedTextMessage = str_replace(
            $tokens, $replacements, $textMessage
        );
        
        $parsedHtmlMessage = str_replace(
            $tokens, $replacements, $htmlMessage
        );

        try {
            $settings = $queueModel->fetchSettings();
            $from = $settings['newsletter_message_queue_settings_from_email'];
            $mail = new Zend_Mail;
            $mail->setFrom($from, 'Sample Mailer');
            $mail->setBodyText($parsedTextMessage);
            $mail->addTo($recipient, $firstName . $middleName . $lastName);
            $mail->setSubject($subject);
            $transport = $queueModel->getNonVerpMailTransport();
            $mail->send($transport);
            $mail->setBodyHtml($parsedHtmlMessage);
            $mail->send($transport);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param array $lists
     */
    public function sendToLists($listIds)
    {
        if (!is_array($listIds)) {
            return;
        }
        $subscriberModel = new Core_Model_Newsletter_Subscriber();
        $queueModel = new Core_Model_Newsletter_Message_Queue; 
        $domainBlacklistModel = new Core_Model_Newsletter_DomainBlacklist;

        foreach ($listIds as $listId) {
            $subscribers = $subscriberModel->fetchAllByListId($listId);
            $queueData = array();
            $duplicateListIds = array();
           
            foreach ($subscribers as $subscriber) {            
                $queueData['subscriber_id'] = $subscriber['subscriber_id'];
                $queueData['message_id'] = $this->_messageId;
                $queueData['list_id'] = $listId;
                $duplicateExists = $queueModel->checkQueueDuplicate(
                    $subscriber['subscriber_id'], 
                    $queueData['list_id'], 
                    $queueData['message_id']
                );
                                            
                if (!$duplicateExists) {
                    $queueModel->create($queueData);
                }
                else {
                    $duplicateListIds[] = $queueData['list_id'];
                }
            }
        }
        return $duplicateListIds;

    }

    /**
     * @param int campaignId
     * @return array the messages record with campaignId
     */
    public function getMessagesByCampaignId($campaignId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('campaign_id = ?', $campaignId);

        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

}

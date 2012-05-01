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
    public function setMessageId($messageId)
    {
        $this->getModel()->setMessageId($messageId);
        return $this;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function sendToLists($data)
    {
        return $this->getModel()->sendToLists($data['lists']);
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @return bool
     */
    public function sendTestMessage($email, $firstName, $middleName, $lastName)
    {
       return  $this->getModel()
                    ->sendTestMessage(
                        $email, 
                        $firstName, 
                        $middleName, 
                        $lastName
                    );
    }

    /**
     * @param array $data
     * @return bool
     */
    public function addToQueue($data)
    {
        $queueModel = new Core_Model_Newsletter_Message_Queue;
        $table = $queueModel->getTable();
        $subscriberModel = new Core_Model_Newsletter_Subscriber();
        $result = $subscriberModel->fetchAllByEmail($data['email']);
        if($result['subscriber_id']) {
            $dataToMessageQueue['subscriber_id'] = $result['subscriber_id'];
        } else {
            $form = new Core_Form_Newsletter_Subscriber_Create;
            $dataToCreateSubscriber['status'] = Core_Model_Newsletter_Subscriber::ACTIVE;
            $dataToCreateSubscriber['first_name'] = $data['first_name'];
            $dataToCreateSubscriber['middle_name'] = $data['middle_name'];
            $dataToCreateSubscriber['last_name'] = $data['last_name'];
            $dataToCreateSubscriber['format'] = $data['format'];
            $dataToCreateSubscriber['email'] = $data['email'];
            if ($form->isValid($dataToCreateSubscriber)) {
                $dataToMessageQueue['subscriber_id'] = $subscriberModel->create($dataToCreateSubscriber);
            }
        }
        try {
            $dataToMessageQueue['status'] = Core_Model_Newsletter_Message_Queue::MESSAGE_NOT_SENT;
            $dataToMessageQueue['custom_text_body'] = $data['custom_text_body'];
            $dataToMessageQueue['custom_html_body'] =$data['custom_html_body'];
            $dataToMessageQueue['custom_subject'] =$data['custom_subject'];
            $table->insert($dataToMessageQueue);
            return true;
        } catch(Exception $exception) {
            return false;
        }
    }

}

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
class Core_Service_WebService_Rest_Newsletter_Message_Queue
{
    /**
     * @var object Core_Model_Newsletter_Message
     */
    protected $_model;

    /**
     * @return object Core_Model_Newsletter_Message
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = new Core_Model_Newsletter_Message;
        }
        return $this->_model;
    }

    /**
     * @param int $messageId
     * @return flunt interface
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

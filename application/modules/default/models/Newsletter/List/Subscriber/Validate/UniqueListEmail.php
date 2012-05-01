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

class Core_Model_Newsletter_List_Subscriber_Validate_UniqueListEmail extends Zend_Validate_Abstract
{
    const EMAIL_EXISTS = 'already exists';

    protected $_messageTemplates = array(
        self::EMAIL_EXISTS => 'This subscriber is already subscribed to list'
    );

   /**
    * @var int listId
    */
    protected $_listId;

    /**
    * @var int listId
    */
    protected $_subscriberId;
    
    /**
     * @param int $listId
     */
    public function __construct($listId, $subscriberId = null)
    {
        $this->_listId = $listId;
        if($subscriberId) {
            $this->_subscriberId = $subscriberId;
        }
    }
   
    /**
     * @string $value email address
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);
        $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $subscriberRecord = $listSubscriberModel->getByListIdAndSubscriberId($this->_listId,$value); 
        
        if ($subscriberRecord == NULL) {
            return true;
        }
        else {
            $this->_error(self::EMAIL_EXISTS);
            return false;
        }
        
    }
}

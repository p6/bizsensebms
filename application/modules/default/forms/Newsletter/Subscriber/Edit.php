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

class Core_Form_Newsletter_Subscriber_Edit extends 
        Core_Form_Newsletter_Subscriber_Create
{
    /**
     *@var string subscriber model
     */
    protected $_subscriberId;
    
    public function __construct($subscriberId)
    {
        $this->_subscriberId = $subscriberId;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $subscriberModel = new Core_Model_Newsletter_Subscriber;
        $defaultValues = $subscriberModel->setSubscriberId($this->_subscriberId)
                                    ->fetch();
        $email = $this->createElement('text', 'email')
                            ->setLabel('Email')
                            ->setRequired(true)
                            ->addValidator(new Zend_Validate_StringLength(0, 320))
                            ->addValidator(new Zend_Validate_EmailAddress())
                            ->addValidator(new Zend_Validate_Db_NoRecordExists(
                                'subscriber', 'email',array(
                                'field' => 'email', 'value' => $defaultValues['email'] ))); 
        $this->addElement($email);
               
        if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::CONFIRMED) {
            $defaultValues['status'] = Core_Model_Newsletter_Subscriber::CONFIRMED;
        }
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::UNCONFIRMED) {
           $defaultValues['status']= Core_Model_Newsletter_Subscriber::UNCONFIRMED;
        }  
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::ACTIVE) {
            $defaultValues['status']= Core_Model_Newsletter_Subscriber::ACTIVE;
        } 
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::BLOCKED) {
            $defaultValues['status']= Core_Model_Newsletter_Subscriber::BLOCKED;
        } 
        if ($defaultValues['format']) {
            $defaultValues['format'] = 1;
        }
        $this->populate($defaultValues);
    }
}

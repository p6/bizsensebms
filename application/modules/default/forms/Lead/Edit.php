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

class Core_Form_Lead_Edit extends Core_Form_Lead_Create
{
    protected $_leadId;

    function __construct($leadId) 
    {
        $this->_leadId = $leadId;
        parent::__construct();
    }
    
    public function init()
    {
        parent::init();
        
        $leadModel = new Core_Model_Lead($this->_leadId);
        $leadDetails = $leadModel->fetch();
        
        $this->getElement('submit')->setLabel('Edit Lead');
        $mobile = $this->createElement('text', 'mobile')
                       ->addValidator(new Zend_Validate_StringLength(3, 40))
                       ->setLabel('Mobile')
                       ->addValidator(new Zend_Validate_Db_NoRecordExists(
                          'lead', 'mobile',array(
                          'field' => 'mobile', 'value' => $leadDetails['mobile'] )));

        $email = $this->createElement('text', 'email')
                      ->setLabel('Email')
                      ->addValidator(new Zend_Validate_StringLength(0, 320))
                      ->addValidator(new Zend_Validate_Db_NoRecordExists(
                         'lead', 'email',array(
                         'field' => 'email', 'value' => $leadDetails['email'] )))                           
                      ->addValidator(new Zend_Validate_Db_NoRecordExists(
                         'contact', 'work_email',array(
                         'field' => 'work_email', 'value' => $leadDetails['email'] )))
                      ->addValidator(new Zend_Validate_EmailAddress());

	    $this->removeElement('no_csrf_lead_create');

	    $hash = $this->createElement('hash', 'no_csrf_lead_edit',
            array(
                'salt' => 'unique',
                'ignore' => true,
            )
        );

        $this->addElements(array($email, $mobile, $hash));     
        
        $contactGroup = $this->addDisplayGroup(array('home_phone', 'work_phone',
            'mobile', 'do_not_call', 'fax', 'email', 
            'email_opt_out'), 'contact');
            
        $lead = new Core_Model_Lead($this->_leadId);
        $rows = $lead->fetch();
        $casted = (array)$rows;
        $this->populate($casted);
    }
}

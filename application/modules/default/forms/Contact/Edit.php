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

class Core_Form_Contact_Edit extends Core_Form_Contact_Create
{
    public $db;
    public $contactId;

    function __construct($contactId = null) 
    {

        $this->db = Zend_Registry::get('db');
        if (is_numeric($contactId)) {
            $this->contactId = $contactId;            
        }
        parent::__construct();
    }

   
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $contact = new Core_Model_Contact($this->contactId);
        $contact = $contact->fetchAsArray();
       
        if ($contact['reports_to']) {
            
        }

        parent::init();

	$this->removeElement('no_csrf_contact_create');

        $this->addElement('hash', 'no_csrf_contact_edit',
            array(
                'salt' => 'unique',
                'ignore' => true,
            )
        );

         $workEmail = $this->createElement('text', 'work_email')
                            ->setLabel('Work Email')
                            ->addValidator(new Zend_Validate_EmailAddress())
	                        ->addValidator(new Zend_Validate_StringLength(0, 320))
	                        ->addValidator(new Zend_Validate_Db_NoRecordExists(
                                'contact', 'work_email',array(
                                'field' => 'work_email', 'value' => $contact['work_email'] )));
         $this->addElements(array($workEmail));
         
         $contactGroup = $this->addDisplayGroup(array('work_phone', 'home_phone', 'mobile', 'fax', 'work_email', 
            'other_email'), 'contact');
         $contactGroup->getDisplayGroup('contact')->setLegend('Contact details');
          
        #var_dump($contact);
        $this->populate($contact);
      
        /*
         * Dojo sets date to 11/30/1899 if 0000-00-00 is passed to populate method of Zend_Form
         * Therefore set the value to '' if the date is not valid
         
        $birthdayInModel = $contact['birthday'];
        if (! (Zend_Date::isDate($birthdayInModel)) ) {
         #   $this->getElement('birthday')->setValue('');
        }
 
        if ($birthdayInModel == '0000-00-00') {
            $this->getElement('birthday')->setValue('');
        }
        */ 
    }   

     
 
  }

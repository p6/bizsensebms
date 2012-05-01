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

class Profile_Form_Edit
{
    public $db;
	public $userId;

    public function __construct($userId)
    {
        $this->db = Zend_Registry::get('db');
		$this->userId = $userId;
    }
    
    public function getForm()
    {
		$profileUtil = new User($this->userId);
		$profile = $profileUtil->fetch();

        $form = new Zend_Form;
        $form->setAction('/profile/editmy')
                ->setMethod('post');

        $firstName = $form->createElement('text', 'firstName')
                            ->setLabel('First Name')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setRequired(true);

        $middleName = $form->createElement('text', 'middleName')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Middle Name');

        $lastName = $form->createElement('text', 'lastName')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Last Name')
                            ->setRequired(true);

        $personalEmail = $form->createElement('text', 'personalEmail')
                                ->addValidator(new Zend_Validate_StringLength(0, 320))
                                ->setLabel('Personal Email');

        $workPhone = $form->createElement('text', 'workPhone')
                            ->addValidator(new Zend_Validate_StringLength(0, 20))
                            ->setLabel('Work Phone');

        $mobilePhone = $form->createElement('text', 'mobilePhone')
                                ->addValidator(new Zend_Validate_StringLength(0, 20))
                                ->setLabel('Mobile Phone');

        $homePhone = $form->createElement('text', 'homePhone')
                            ->addValidator(new Zend_Validate_StringLength(0, 20))
                            ->setLabel('Home Phone');

        $branchId = new Zend_Dojo_Form_Element_FilteringSelect('branchId');
        $branchId->setLabel('Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branchName")
                ->setRequired(true);


        /*
         *  Load defaults
         */
        if ($profile) {
            $branchId->setValue($profile->branchId);
            $homePhone->setValue($profile->homePhone);
            $mobilePhone->setValue($profile->mobilePhone);
            $workPhone->setValue($profile->workPhone);
            $personalEmail->setValue($profile->personalEmail);
            $lastName->setValue($profile->lastName);
            $middleName->setValue($profile->middleName);
            $firstName->setValue($profile->firstName);
        }

        $submit = $form->createElement('submit', 'Submit');

        $form->addElements(array($firstName, $middleName, $lastName, $personalEmail, $workPhone, $mobilePhone,
            $homePhone, $branchId, $submit));

        return $form;

    }
}



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

class Core_Form_User_AddUser extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        $firstName = $this->createElement('text', 'first_name')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('First Name');

        $middleName = $this->createElement('text', 'middle_name')
                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('Middle Name');

        $lastName = $this->createElement('text', 'last_name')
                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setRequired(true)
                        ->setLabel('Last Name');

        $username = $this->createElement('text', 'username')
                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_Db_NoRecordExists(
                                                      'user', 'username'))
                        ->setLabel('Username');
        
        $email = $this->createElement('text', 'email')
                            ->setLabel('Email address')
                            ->setRequired(true)
                            ->addValidator(new Zend_Validate_EmailAddress())
                            ->addValidator(new Core_Model_User_Validate_UniqueUserEmail())
                            ->addValidator(new Zend_Validate_Db_NoRecordExists('user', 'email'));

        $password = $this->createElement('password', 'password')
                            ->setLabel('Password')
                            ->setDescription('Minimum 6 characters. Consider using combination of alphabets, digits and special characters')
                            ->addValidator(new Zend_Validate_StringLength(6, 50))
                            ->setRequired(true);

        $confirm_password = $this->createElement('password', 'confirm_password' )
                                ->setLabel('Confirm Password')
                                ->setRequired(true)
                                ->addValidator(new Bare_Validate_MatchPasswords);

                                

        $branchId = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
        $branchId->setLabel('Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branch_name")
                ->setRequired(true);

        $reportsTo = new Zend_Dojo_Form_Element_FilteringSelect('reports_to');
        $reportsTo->setLabel('Reports To')
                ->setAutoComplete(true)
                ->setStoreId('userStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/profile'))
                ->setAttrib("searchAttr", "email")
                ->setAttrib("labelAttr", "first_name")
                ->setRequired(false);

        $primaryRole = new Zend_Dojo_Form_Element_FilteringSelect('primary_role');
        $primaryRole->setLabel('Primary role')
                    ->setAutoComplete(true)
                    ->setStoreId('roleStore')
                    ->setStoreType('dojo.data.ItemFileReadStore')
                    ->setStoreParams(array('url'=>'/jsonstore/primaryrole'))
                    ->setAttrib('searchAttr', 'name')
                    ->setRequired(true); 

        $personalEmail = $this->createElement('text', 'personal_email')
                                ->setLabel('Personal Email')
                                ->addValidator(new Zend_Validate_StringLength(0, 320))
                                ->addValidator(new Zend_Validate_EmailAddress());

        $workPhone = $this->createElement('text', 'work_phone')
                                ->setLabel('Work phone')
                                ->addValidator(new Zend_Validate_StringLength(0, 20));

        $homePhone = $this->createElement('text', 'home_phone')
                                ->setLabel('Home phone')
                                ->addValidator(new Zend_Validate_StringLength(0, 20));

        $mobilePhone = $this->createElement('text', 'mobile')
                                ->setLabel('Mobile phone')
                                ->addValidator(new Zend_Validate_StringLength(0, 20));
        
        $employeeNumber = $this->createElement('text', 'employee_number')
                                ->setLabel('Employee number')
                                ->addValidator(new Zend_Validate_StringLength(0, 50));
        
        $pfNumber = $this->createElement('text', 'pf_number')
                                ->setLabel('PF number')
                                ->addValidator(new Zend_Validate_StringLength(0, 50));  
        
        $esiNumber = $this->createElement('text', 'esi_number')
                                ->setLabel('ESI number')
                                ->addValidator(new Zend_Validate_StringLength(0, 50));
                                
        $bloodGroup = new Zend_Form_Element_Select('blood_group');
        $bloodGroup->setLabel('Blood Group');
        $bloodGroup->addMultiOption('O+', 'O Positive');
        $bloodGroup->addMultiOption('O-', 'O Negative');
        $bloodGroup->addMultiOption('A+', 'A Positive');
        $bloodGroup->addMultiOption('A-', 'A Negative');
        $bloodGroup->addMultiOption('B+', 'B Positive');
        $bloodGroup->addMultiOption('B-', 'B Negative');
        $bloodGroup->addMultiOption('AB+', 'AB Positive');
        $bloodGroup->addMultiOption('AB-', 'AB Negative');
                                       
                                                  
        $notifyUser = $this->createElement('checkbox', 'notify_user')
                            ->setDescription('If checked the newly created user will be notified via email')
                            ->setLabel('Notify user');

        
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');
        $this->addElements(array($firstName, $middleName, $lastName, $username, $email, $password, $confirm_password, 
                    $primaryRole, $reportsTo, $branchId, 
                    $personalEmail, $workPhone, $homePhone, $mobilePhone, $bloodGroup,
                    $employeeNumber, $pfNumber, $esiNumber, $notifyUser, 
                    $submit));

        new BV_Filter_AddStripTagToElements($this);
        return $this;
    }

}

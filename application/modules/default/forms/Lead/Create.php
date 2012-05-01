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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Lead_Create extends Zend_Form
{
    public function init()
    {

        $db = Zend_Registry::get('db');
        $this->setMethod('post');

        $source = new Core_Form_Lead_Element_LeadSource;
        $leadSourceId = $source->getElement();

        $status = new Core_Form_Lead_Element_LeadStatus;
        $leadStatusId = $status->getElement();

        $firstName = $this->createElement('text', 'first_name')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('First Name');

        $middleName = $this->createElement('text', 'middle_name')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Middle Name');

        $lastName = $this->createElement('text', 'last_name')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Last Name')
                            ->setAllowEmpty(false)
                            ->addValidator(new Bare_Validate_PersonName)
                            ->setDescription('Both First name and last name cannot be blank');


        $companyName = $this->createElement('text', 'company_name')
                            ->addValidator(new Zend_Validate_StringLength(2, 100))
                                ->setLabel('Company Name');

        $homePhone = $this->createElement('text', 'home_phone')
                        ->addValidator(new Zend_Validate_StringLength(3, 40))
                        ->setLabel('Home Phone');

        $workPhone = $this->createElement('text', 'work_phone')
                        ->addValidator(new Zend_Validate_StringLength(3, 40))
                        ->setLabel('Work Phone');

        $doNotCall = $this->createElement('checkbox', 'do_not_call')
                            ->setLabel('Do not call');

        $mobile = $this->createElement('text', 'mobile')
                            ->addValidator(new Zend_Validate_StringLength(3, 40))
                            ->addValidator(new Zend_Validate_Db_NoRecordExists('lead', 'mobile'))
                            ->setLabel('Mobile');

        $fax = $this->createElement('text', 'fax')
                         ->addValidator(new Zend_Validate_StringLength(3, 40))
                        ->setLabel('Fax');

        $email = $this->createElement('text', 'email')
                            ->setLabel('Email')
                            ->addValidator(new Zend_Validate_StringLength(0, 320))
                            ->addValidator(new Zend_Validate_Db_NoRecordExists('lead', 'email'))
                            ->addValidator(new Zend_Validate_Db_NoRecordExists('contact', 'work_email'))
                            ->addValidator(new Zend_Validate_EmailAddress());


        $emailOptOut = $this->createElement('checkbox', 'email_opt_out')
                            ->setLabel('Opt Out');


        $addressLine1 = $this->createElement('text', 'address_line_1')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Address Line 1')
                            ->setRequired(false);

        $addressLine2 = $this->createElement('text', 'address_line_2')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Address Line 2')
                            ->setRequired(false);

        $addressLine3 = $this->createElement('text', 'address_line_3')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Address Line 3')
                            ->setRequired(false);

        $addressLine4 = $this->createElement('text', 'address_line_4')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Address Line 4')
                            ->setRequired(false);


        $city = $this->createElement('text', 'city')
                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('City');

        $state = $this->createElement('text', 'state')
                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('State');

        $postalCode = $this->createElement('text', 'postal_code')
                            ->addValidator(new Zend_Validate_StringLength(2, 40))
                            ->setLabel('Postal code');

        $country = $this->createElement('text', 'country')
                         ->setLabel('Country');

        $description = $this->createElement('textarea', 'description')
                                ->setLabel('Description')
                                ->addValidator(new Zend_Validate_StringLength(2, 250))
                                ->setAttribs(array(
                                    'rows' => 5,
                                    'cols' => 40
                                  ));
        

        if (Core_Model_User_Current::getId()) {
            $user = new Core_Model_User;

            $userData = $user->fetch(); 
            $userEmail = $userData->email;
            $userBranch = $userData->branch_name;
        } else {
            $userEmail = '';
            $userBranch = '';
        }
                
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Lead To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);
        
        $element = new Core_Form_Branch_Element_Branch;
        $branchId = $element->getElement();
        $branchId->setRequired(true);
        
        $campaignElementContainer = new Core_Form_Campaign_Element_Campaign;
        $campaignId = $campaignElementContainer->getElement();

        $submit = $this->createElement('submit', 'submit')
                        ->setAttrib('class', 'submit_button');

        $this->addElements(array($leadSourceId, $leadStatusId, $firstName, $middleName, $lastName, $companyName, $homePhone,
            $workPhone, $doNotCall, $mobile, $fax, $email, $emailOptOut, $addressLine1, $addressLine2, $addressLine3, 
            $addressLine4, $city, $state, $postalCode, $country, $description, $campaignId, $assignedTo, $branchId, $submit));

        

        $namesGroup = $this->addDisplayGroup(array('first_name', 'middle_name', 'last_name', 'company_name'), 'names');
        $namesGroup->getDisplayGroup('names')->setLegend('Name');

        $contactGroup = $this->addDisplayGroup(array('home_phone', 'work_phone', 'mobile', 'do_not_call', 'fax', 'email', 
            'email_opt_out'), 'contact');
        $contactGroup->getDisplayGroup('contact')->setLegend('Contact Information');

        $addressGroup = $this->addDisplayGroup(array('address_line_1', 'address_line_2', 'address_line_3', 'address_line_4',
            'city', 'state', 'postal_code', 'country'), 'address');
        $addressGroup->getDisplayGroup('address')->setLegend('Address');
        
        $metaDataGroup = $this->addDisplayGroup(array('lead_source_id', 'lead_status_id', 'campaign_id', 'assigned_to', 'branch_id', 
            'description'), 'metaData');
        $metaDataGroup->getDisplayGroup('metaData')->setLegend('Lead Meta Data');

        $this->addDisplayGroup(array('submit'), 'submit');

        $this->setElementFilters(array(new Zend_Filter_StringTrim()));

    }

}


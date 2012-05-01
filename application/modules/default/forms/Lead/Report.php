<?php
/*
 * Lead report form
 * IN DEVELOPMENT
 * Add validators
 * Add access checks
 *
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Lead_Report extends Zend_Form
{

    public function init()
    {
        $this->setAction('/reports/lead');
        $this->setMethod('post');
        $this->setName('leadReport');
        
        $firstName = $this->createElement('text', 'firstName')
                            ->setLabel('First name');
        $middleName = $this->createElement('text', 'middleName')
                            ->setLabel('Middle name');
        $lastName = $this->createElement('text', 'lastName')
                            ->setLabel('Last name');
        $name = $this->createElement('text', 'name')
                        ->setLabel('Search in all')
                        ->setDescription('If used, first name, middle name and last name fields will be ignored');

        $mobile = $this->createElement('text', 'mobile')
                            ->setLabel('Mobile');
        $fax = $this->createElement('text', 'fax')
                            ->setLabel('Fax');
        $email = $this->createElement('text', 'email')
                            ->setLabel('Email');
        $homePhone = $this->createElement('text', 'homePhone')
                            ->setLabel('Home phone');
        $workPhone = $this->createElement('text', 'workPhone')
                            ->setLabel('Work phone');
        $companyName = $this->createElement('text', 'companyName')
                            ->setLabel('Company name');
        $addressLine1 = $this->createElement('text', 'addressLine1')
                            ->setLabel('Address Line 1');
        $addressLine2 = $this->createElement('text', 'addressLine2')
                            ->setLabel('Address Line 2');
        $addressLine3 = $this->createElement('text', 'addressLine3')
                            ->setLabel('Address Line 3');
        $addressLine4 = $this->createElement('text', 'addressLine4')
                            ->setLabel('Address Line 4');
        $city = $this->createElement('text', 'city')
                            ->setLabel('City');
        $state = $this->createElement('text', 'state')
                            ->setLabel('State');
        $postalCode = $this->createElement('text', 'postalCode')
                            ->setLabel('Postal Code');
        $country = $this->createElement('text', 'country')
                            ->setLabel('Country');

        $doNotCall = new BV_Form_Element_DoNotCallMultiselect;
        $doNotCall = $doNotCall->getElement();

        $emailOptOut = new BV_Form_Element_EmailOptOutMultiselect;
        $emailOptOut = $emailOptOut->getElement();

        $converted = new BV_Form_Element_ConvertedMultiselect;
        $converted = $converted->getElement();

        $createdFrom = new Zend_Dojo_Form_Element_DateTextBox('createdFrom');
        $createdFrom->setLabel('Between');

        $createdTo = new Zend_Dojo_Form_Element_DateTextBox('createdTo');
        $createdTo->setLabel('And');
        
        $lastUpdatedFrom = new Zend_Dojo_Form_Element_DateTextBox('lastUpdatedFrom');
        $lastUpdatedFrom->setLabel('Between');

        $lastUpdatedTo = new Zend_Dojo_Form_Element_DateTextBox('lastUpdatedTo');
        $lastUpdatedTo->setLabel('And');

        $leadSourceId = new Core_Form_Lead_Element_LeadSourceMultiselect;
        $leadSourceId = $leadSourceId->getElement();        

        $leadStatusId = new Core_Form_Lead_Element_LeadStatusMultiselect;
        $leadStatusId = $leadStatusId->getElement();        

        $branchId = new Core_Form_Branch_Element_BranchMultiselect;
        $branchId = $branchId->getElement();

        $roleId = new Core_Form_User_Element_RolesMultiselect;
        $roleId = $roleId->getElement();

        $assignedTo = new Core_Form_User_Element_User;
        $assignedTo = $assignedTo->getElement();
    
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');
       
        $this->addElements(array($firstName, $middleName, $lastName, $name, $homePhone, $mobile, $fax, $email, $workPhone, 
            $companyName, $addressLine1, $addressLine2, $addressLine3, $addressLine4, $city, $state, $postalCode, 
            $country, $doNotCall, $emailOptOut, $converted, $createdFrom, $createdTo, $lastUpdatedFrom, $lastUpdatedTo,
            $leadSourceId, $leadStatusId, $branchId, $roleId, $assignedTo, $submit));

        $nameGroup = $this->addDisplayGroup(array('firstName', 'middleName', 'lastName', 'name'), 'nameGroup');
        $personalContactGroup = $this->addDisplayGroup(array('mobile', 'fax', 'email'), 'personalContactGroup');
        $phoneContactGroup = $this->addDisplayGroup(array('homePhone', 'workPhone', 'companyName'), 'phoneContactGroup');
        $addressGroup1 = $this->addDisplayGroup(array('addressLine1', 'addressLine2'), 'addressGroup1');
        $addressGroup2 = $this->addDisplayGroup(array('addressLine3', 'addressLine4'), 'addressGroup2');
        $cityGroup = $this->addDisplayGroup(array('city', 'state'), 'cityGroup');
        $stateGroup = $this->addDisplayGroup(array('postalCode', 'country'), 'stateGroup');
        $optIn = $this->addDisplayGroup(array('doNotCall', 'emailOptOut', 'converted'), 'optInGroup');
        $createdGroup = $this->addDisplayGroup(array('createdFrom', 'createdTo'), 'createdGroup');
        $lastUpdatedGroup = $this->addDisplayGroup(array('lastUpdatedFrom', 'lastUpdatedTo'), 'lastUpdatedGroup');
        $leadMetaGroup = $this->addDisplayGroup(array('lead_source_id', 'lead_status_id'), 'leadMetaGroup');
        $ownerGroup = $this->addDisplayGroup(array('branchId', 'roleId', 'assigned_to'), 'assignedGroup');

        $submitGroup = $this->addDisplayGroup(array('submit'), 'submitGroup');        


        $lineElements = array($firstName, $middleName, $lastName, $homePhone, $mobile, $fax, $email, $workPhone, 
            $companyName, $addressLine1, $addressLine2, $addressLine3, $addressLine4, $city, $state, $postalCode, 
            $country);
        foreach ($lineElements as $element) {
            $element->setAttrib('size', '18');
            $element->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
        ));

        }

        $lineElementsWithoutSizeAttrib = array($leadSourceId, $leadStatusId);
        foreach ($lineElementsWithoutSizeAttrib as $element) {
            $element->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
            ));
        }

       

        $dojoLineItems = array($createdFrom, $createdTo, $lastUpdatedFrom, $lastUpdatedTo);
        foreach ($dojoLineItems as $element) {
            $element->setDecorators(array(
                'DijitElement',
                'Description',
                'Errors',
                array('Label'),
            ));
        }    

        
        $nameGroup->getDisplayGroup('nameGroup')->setAttrib('class','searchFieldSet');
        $personalContactGroup->getDisplayGroup('personalContactGroup')->setAttrib('class','searchFieldSet');
        $phoneContactGroup->getDisplayGroup('phoneContactGroup')->setAttrib('class','searchFieldSet');
        $addressGroup1->getDisplayGroup('addressGroup1')->setAttrib('class','searchFieldSet');
        $addressGroup2->getDisplayGroup('addressGroup2')->setAttrib('class','searchFieldSet');
        $cityGroup->getDisplayGroup('cityGroup')->setAttrib('class','searchFieldSet');
        $stateGroup->getDisplayGroup('stateGroup')->setAttrib('class','searchFieldSet');
        $createdGroup->getDisplayGroup('createdGroup')->setAttrib('class','searchFieldSet')
                    ->setLegend('Created date');
        $lastUpdatedGroup->getDisplayGroup('lastUpdatedGroup')->setAttrib('class','searchFieldSet')
                    ->setLegend('Last updated date');
        $leadMetaGroup->getDisplayGroup('leadMetaGroup')->setAttrib('class','searchFieldSet');


    }

}


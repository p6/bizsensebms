<?php
/*
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
class Core_Form_Contact_Create extends Zend_Form
{


    public function init()
    {
        $db = Zend_Registry::get('db'); 
        $this->setAction('/contact/create');
        $this->setMethod('post');

        $salutationId = $this->createElement('select', 'salutation_id')
               ->setLabel('Salutation');
        $salutationId->addMultiOption("", "");
        $sql = "SELECT name, salutation_id FROM salutation";
        $result = $db->fetchAll($sql);

        foreach ($result as $row) {
            $salutationId->addMultiOption($row->salutation_id, $row->name);
        }

    
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

        $workPhone = $this->createElement('text', 'work_phone')
                          ->setLabel('Work Phone')
	                      ->addValidator(new Zend_Validate_StringLength(0, 20));
    
        $homePhone = $this->createElement('text', 'home_phone')
                          ->setLabel('Home Phone')
	                      ->addValidator(new Zend_Validate_StringLength(0, 20));

        $mobile = $this->createElement('text', 'mobile')
                        ->setLabel('Mobile')
	                    ->addValidator(new Zend_Validate_StringLength(0, 20));

        $fax = $this->createElement('text', 'fax')
                    ->setLabel('Fax')
	                ->addValidator(new Zend_Validate_StringLength(0, 20));

        $workEmail = $this->createElement('text', 'work_email')
                            ->setLabel('Work Email')
                            ->addValidator(new Zend_Validate_EmailAddress())
	                        ->addValidator(new Zend_Validate_StringLength(0, 320))
	                        ->addValidator(new Zend_Validate_Db_NoRecordExists('contact', 'work_email'));

        $otherEmail = $this->createElement('text', 'other_email')
                            ->setLabel('Other Email')
                            ->addValidator(new Zend_Validate_EmailAddress())
	                        ->addValidator(new Zend_Validate_StringLength(0, 320));

        $billingAddressLine1 = $this->createElement('text', 'billing_address_line_1')
                                    ->setLabel('Billing Address Line 1')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50));

        $billingAddressLine2 = $this->createElement('text', 'billing_address_line_2')
                                    ->setLabel('Billing Address Line 2')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50));

        $billingAddressLine3 = $this->createElement('text', 'billing_address_line_3')
                                    ->setLabel('Billing Address Line 3')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50));

        $billingAddressLine4 = $this->createElement('text', 'billing_address_line_4')
                                    ->setLabel('Billing Address Line 4')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50));
                                    
                                    
        $copyFromBilling = $this->createElement('checkbox', 'copy_from_billing');
        $copyFromBilling->setLabel('Copy from billing address')
                        ->setAttrib('onclick','copyBillingAddress()')   
                        ->setIgnore(true);

        $shippingAddressLine1 = $this->createElement('text', 'shipping_address_line_1')
                                        ->setLabel('Shipping Address Line 1')
                                        ->addValidator(new Zend_Validate_StringLength(0, 50));

        $shippingAddressLine2 = $this->createElement('text', 'shipping_address_line_2')
                                        ->setLabel('Shipping Address Line 2')
                                        ->addValidator(new Zend_Validate_StringLength(0, 50));

        $shippingAddressLine3 = $this->createElement('text', 'shipping_address_line_3')
                                        ->setLabel('Shipping Address Line 3')
                                        ->addValidator(new Zend_Validate_StringLength(0, 50));

        $shippingAddressLine4 = $this->createElement('text', 'shipping_address_line_4')
                                        ->setLabel('Shipping Address Line 4')
                                        ->addValidator(new Zend_Validate_StringLength(0, 50));

        $billingCity = $this->createElement('text', 'billing_city')
                            ->setLabel('Billing City')
	                        ->addValidator(new Zend_Validate_StringLength(0, 100));
    
        $shippingCity = $this->createElement('text', 'shipping_city')
                                ->setLabel('Shipping City')
	                            ->addValidator(new Zend_Validate_StringLength(0, 100));

        $billingState = $this->createElement('text', 'billing_state')
                                ->setLabel('Billing State')
	                            ->addValidator(new Zend_Validate_StringLength(0, 100));

        $shippingState = $this->createElement('text', 'shipping_state')
                                ->setLabel('Shipping State')
	                            ->addValidator(new Zend_Validate_StringLength(0, 100));

        $billingPostalCode = $this->createElement('text', 'billing_postal_code')
                                    ->setLabel('Billing Postal code')
	                                ->addValidator(new Zend_Validate_StringLength(0, 20));

        $shippingPostalCode = $this->createElement('text', 'shipping_postal_code')
                                    ->setLabel('Shipping Postal code')
	                                ->addValidator(new Zend_Validate_StringLength(0, 20));

        $billingCountry = $this->createElement('text', 'billing_country')
                                ->setLabel('Billing Country')
	                            ->addValidator(new Zend_Validate_StringLength(0, 100));

        $shippingCountry = $this->createElement('text', 'shipping_country')
                                ->setLabel('Shipping Country')
	                            ->addValidator(new Zend_Validate_StringLength(0, 100));

        $description = $this->createElement('textarea', 'description')
                            ->setLabel('Description')
	                        ->addValidator(new Zend_Validate_StringLength(0, 250))
                            ->setAttribs(array(
                                    'rows' => 5,
                                    'cols' => 40
                                )); 
        $submit = $this->createElement('submit', 'submit')
                      ->setAttrib('class', 'submit_button');

        $birthday = new Zend_Dojo_Form_Element_DateTextBox('birthday');
        $birthday->setLabel('Birthday');

        $birthdayDate = $this->createElement('select', 'birthday_date');
        $birthdayDate->setLabel('Birthday date');
        $birthdayDate->addMultiOptions(array("0"=>"Select"));
        for($i=1;$i<=31;$i++) {
            $birthdayDate->addMultiOptions(array("$i"=>"$i"));
        }

        $birthdayMonth = $this->createElement('select', 'birthday_month');
        $birthdayMonth->setLabel('Birthday month');
        $birthdayMonth->addMultiOptions(array("0"=>"Select"));
        $birthdayMonth->addMultiOptions(array('1'=>'January', '2'=>'Febraury', '3'=>'March',
                '4'=>'April', '5'=>'May', '6'=>'June', '7'=>'July', '8'=>'August',
                '9'=>'September', '10'=>'October', '11'=>'November', 
                '12'=>'December'));
    
        $title = $this->createElement('text', 'title')
		                ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('Title');
    
        $department = $this->createElement('text', 'department')
		                    ->addValidator(new Zend_Validate_StringLength(0, 100))
           	                ->setLabel('Department');


        $doNotCall = $this->createElement('checkbox', 'do_not_call')
                            ->setLabel('Do not call');

        $emailOptOut = $this->createElement('checkbox', 'email_opt_out')
                            ->setLabel('Email Opt Out');

        $assistantId = new Zend_Dojo_Form_Element_FilteringSelect('assistant_id');
        $assistantId->setLabel('Assistant')
                    ->setAutoComplete(true)
                    ->setStoreId('assistantStore')
                    ->setStoreType('dojo.data.ItemFileReadStore')
                    ->setStoreParams(array('url'=>'/contact/jsonstore'))
                    ->setAttrib("searchAttr", "first_name");

        $reportsTo = new Zend_Dojo_Form_Element_FilteringSelect('reports_to');
        $reportsTo->setLabel('Reports To')
                    ->setAutoComplete(true)
                    ->setStoreId('reportsToStore')
                    ->setStoreType('dojo.data.ItemFileReadStore')
                    ->setStoreParams(array('url'=>'/contact/jsonstore'))
                    ->setAttrib("searchAttr", "first_name");
        
        $accountId = new Zend_Dojo_Form_Element_FilteringSelect('account_id');
        $accountId->setLabel('Referece To Account')
                ->setAutoComplete(true)
                ->setStoreId('stateStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/account/jsonstore'))
                ->setAttrib("searchAttr", "account_name")
                ->setAttrib("onChange", "getAddresses(this.value)");

        $campaignElementContainer = new Core_Form_Campaign_Element_Campaign;
        $campaignId = $campaignElementContainer->getElement();

        $user = new Core_Model_User; 
        $user = $user->fetch();
        $userEmail = $user->email;

        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Contact To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);
        
        $userBranch = $user->branch_name;
        
        $branch = new Core_Form_Branch_Element_Branch;
        $branchId = $branch->getElement();
        $branchId->setRequired(true);

    
        $this->addElements(array
            (
                $salutationId, $firstName, $middleName, 
                $lastName, $title, $department,
                $doNotCall, $emailOptOut, $reportsTo, $assistantId, 
                $workPhone, $homePhone, $mobile, $fax, 
                $workEmail, $otherEmail, $billingAddressLine1, 
                $billingAddressLine2, $billingAddressLine3, 
                $billingAddressLine4, $billingCity, $billingState,
                $billingPostalCode,
                $billingCountry, $copyFromBilling, $shippingAddressLine1, 
                $shippingAddressLine2, $shippingAddressLine3, 
                $shippingAddressLine4,  $shippingCity, 
                $shippingState, $shippingPostalCode,
                $shippingCountry, $description, $birthdayDate, $birthdayMonth, 
                $assignedTo, $campaignId, $accountId, $branchId, $submit
            )
        );


        /**
         * Group the elements and set appropriate legends
         */
        
        $namesGroup = $this->addDisplayGroup(array('salutation_id', 'first_name', 'middle_name', 'last_name', 'birthday_date', 'birthday_month'), 
            'names');
        $namesGroup->getDisplayGroup('names')->setLegend('Name');

        $workGroup = $this->addDisplayGroup(array('title', 'department', 'do_not_call', 'email_opt_out', 'reports_to', 
            'assistant_id'), 'workRelated');
        $workGroup->getDisplayGroup('workRelated')->setLegend('Work related');

        $contactGroup = $this->addDisplayGroup(array('work_phone', 'home_phone', 'mobile', 'fax', 'work_email', 
            'other_email'), 'contact');
        $contactGroup->getDisplayGroup('contact')->setLegend('Contact details');
        
        $billingGroup = $this->addDisplayGroup(array('billing_address_line_1', 'billing_address_line_2', 
            'billing_address_line_3', 'billing_address_line_4', 'billing_city', 'billing_state', 
            'billing_postal_code', 'billing_country'), 'billing');
        $billingGroup->getDisplayGroup('billing')->setLegend('Billing Address');

        $shippingGroup = $this->addDisplayGroup(array('copy_from_billing', 'shipping_address_line_1',
            'shipping_address_line_2', 
            'shipping_address_line_3', 'shipping_address_line_4', 'shipping_city', 'shipping_state', 
            'shipping_postal_code', 'shipping_country'), 'shipping');
        $shippingGroup->getDisplayGroup('shipping')->setLegend('Shipping Address');

        $metaGroup = $this->addDisplayGroup(array('description', 'campaign_id', 'assigned_to', 'account_id', 'branch_id'), 'meta');
        $metaGroup->getDisplayGroup('meta')->setLegend('Meta');

        $submitGroup = $this->addDisplayGroup(array('submit'), 'submit');
        $submitGroup->getDisplayGroup('submit')->setLegend('submit');

        $this->setElementFilters(array(new Zend_Filter_StringTrim()));
        return $this;
    }

}

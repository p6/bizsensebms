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
class Core_Form_Account_Create extends Zend_Form
{
    /*
     * @return Zend_Form
     * Web this to create account
     */
    public function init()
    {

        $accountName = $this->createElement('text', 'account_name')
                            ->setLabel('Account Name')
                            ->addValidator(new Zend_Validate_StringLength(0, 250))
                            ->setRequired(true);

        $website = $this->createElement('text', 'website')
                        ->setLabel('Website URL')
                        ->addValidator(new Zend_Validate_StringLength(0, 200))
                        ->addValidator(new Bare_Validate_Uri)
                        ->setValue('');

        $phone = $this->createElement('text', 'phone')
                        ->addValidator(new Zend_Validate_StringLength(0, 20))
                        ->setLabel('Phone');

        $mobile = $this->createElement('text', 'mobile')
                        ->addValidator(new Zend_Validate_StringLength(0, 20))
                        ->setLabel('Mobile');

        $fax = $this->createElement('text', 'fax')
                        ->addValidator(new Zend_Validate_StringLength(0, 20))
                        ->setLabel('Fax');

        $email = $this->createElement('text', 'email');
        $email->setLabel('Email');
        $email->addValidator(new Zend_Validate_EmailAddress());

        $billingAddressLine1 = $this->createElement('text', 'billing_address_line_1')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Billing Address Line 1');

        $billingAddressLine2 = $this->createElement('text', 'billing_address_line_2')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Billing Address Line 2');

        $billingAddressLine3 = $this->createElement('text', 'billing_address_line_3')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Billing Address Line 3');

        $billingAddressLine4 = $this->createElement('text', 'billing_address_line_4')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Billing Address Line 4');

        $copyFromBilling = $this->createElement('checkbox', 'copy_from_billing');
        $copyFromBilling->setLabel('Copy from billing address')
                        ->setAttrib('onclick','copyBillingAddress()')   
                        ->setIgnore(true);


        $shippingAddressLine1 = $this->createElement('text', 'shipping_address_line_1')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Shipping Address Line 1');

        $shippingAddressLine2 = $this->createElement('text', 'shipping_address_line_2')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Shipping Address Line 2');

        $shippingAddressLine3 = $this->createElement('text', 'shipping_address_line_3')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Shipping Address Line 3');

        $shippingAddressLine4 = $this->createElement('text', 'shipping_address_line_4')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Shipping Address Line 4');

        $billingCity = $this->createElement('text', 'billing_city')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Billing City');

        $shippingCity = $this->createElement('text', 'shipping_city')
                                    ->addValidator(new Zend_Validate_StringLength(0, 50))
                                    ->setLabel('Shipping City');

        $billingState = $this->createElement('text', 'billing_state')
                                 ->addValidator(new Zend_Validate_StringLength(0, 50))
                                ->setLabel('Billing State');

        $shippingState = $this->createElement('text', 'shipping_state')
                                 ->addValidator(new Zend_Validate_StringLength(0, 50))
                                 ->setLabel('Shipping State');

        $billingPostalCode = $this->createElement('text', 'billing_postal_code')
                                    ->addValidator(new Zend_Validate_StringLength(0, 20))
                                    ->setLabel('Billing Postal code');

        $shippingPostalCode = $this->createElement('text', 'shipping_postal_code')
                                    ->addValidator(new Zend_Validate_StringLength(0, 20))
                                    ->setLabel('Shipping Postal code');

        $billingCountry = $this->createElement('text', 'billing_country')
                                 ->addValidator(new Zend_Validate_StringLength(0, 50))
                                ->setLabel('Billing Country');

        $shippingCountry = $this->createElement('text', 'shipping_country')
                                 ->addValidator(new Zend_Validate_StringLength(0, 50))
                                 ->setLabel('Shipping Country');

        $description = $this->createElement('textarea', 'description')
                             ->addValidator(new Zend_Validate_StringLength(0, 250))
                            ->setLabel('Description')
                            ->setAttribs(array(
                                            'rows' => 5,
                                            'cols' => 40
                                        ));
        $tin = $this->createElement('text', 'tin')
                                 ->addValidator(new Zend_Validate_StringLength(0, 100))
                                 ->setLabel('TIN');
        
        $pan = $this->createElement('text', 'pan')
                                 ->addValidator(new Zend_Validate_StringLength(0, 100))
                                 ->setLabel('PAN');
                                 
        $service_tax_number = $this->createElement('text', 'service_tax_number')
                                 ->addValidator(new Zend_Validate_StringLength(0, 100))
                                 ->setLabel('Service Tax Number');

        $campaignElementContainer = new Core_Form_Campaign_Element_Campaign;
        $campaignId = $campaignElementContainer->getElement();
       
        /**
         * Populate default email address and branch name 
         * To assignedTo and branchId 
         */ 
        $user = new Core_Model_User;
        $userData = $user->fetch();

        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Account To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);
        
        $element = new Core_Form_Branch_Element_Branch;
        $branchId = $element->getElement();
        $branchId->setRequired(true);
 
        $submit = $this->createElement('submit', 'submit')
                        ->setAttrib('class', 'submit_button');

        $this->addElements(
            array(
                $accountName, $website, $phone, $mobile, 
                $fax, $email,
                $billingAddressLine1,  $billingAddressLine2, 
                $billingAddressLine3, $billingAddressLine4,
                $billingCity, $billingState,
                $billingPostalCode, $billingCountry, 
                $copyFromBilling,
                $shippingAddressLine1, $shippingAddressLine2, 
                $shippingAddressLine3, $shippingAddressLine4, 
                $shippingCity,  $shippingState, $shippingPostalCode,
                $shippingCountry, $description, $campaignId, $tin, $pan,
                $service_tax_number, $assignedTo, $branchId, $submit
            )
        );
        
        $namesGroup = $this->addDisplayGroup(
            array(
                'account_name', 
                'website', 
                'phone', 
                'mobile', 
                'fax', 
                'email'
            ), 'contact'
        );

        $this->getDisplayGroup('contact')->setLegend('Name and contact');
        
        $billingGroup = $this->addDisplayGroup(
            array(
                'billing_address_line_1', 
                'billing_address_line_2', 
                'billing_address_line_3', 
                'billing_address_line_4', 
                'billing_city', 
                'billing_state', 
                'billing_postal_code', 
                'billing_country'
            ), 'billingGroup'
        );

        $this->getDisplayGroup('billingGroup')->setLegend('Billing Address');

        $shippingGroup = $this->addDisplayGroup(
            array(
                'copy_from_billing',
                'shipping_address_line_1', 
                'shipping_address_line_2', 
                'shipping_address_line_3', 
                'shipping_address_line_4', 
                'shipping_city', 
                'shipping_state', 
                'shipping_postal_code', 
                'shipping_country'
            ), 'shippingGroup'
        );

        $this->getDisplayGroup('shippingGroup')->setLegend('Shipping Address');

        $metaDataGroup = $this->addDisplayGroup(
            array(
                'description',
                'campaign_id', 
                'tin',
                'pan',
                'service_tax_number',
                'assigned_to', 
                'branch_id'
             ), 'metaData'
        );
        $metaDataGroup->getDisplayGroup('metaData')->setLegend('Meta Data');

        $this->addDisplayGroup(array('submit'), 'submit');

        /*
         * Add filters to all the elements
         * To trim the strings 
         * And to filter the HTML tags
         */
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->addFilter('StringTrim');
            $element->addFilter('StripTags');
        }

       return $this;

    }
    
}

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

	$hash = $this->createElement('hash', 'no_csrf_account_create',
            array(
                'salt' => 'unique',
                'ignore' => true,
            )
        );

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
                $service_tax_number, $assignedTo, $branchId, $submit, $hash
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

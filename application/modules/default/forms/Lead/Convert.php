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
class Core_Form_Lead_Convert extends Zend_Form
{
    public $db;
    protected $_leadId;
    
    public function __construct($leadId = null)
    {
        $this->db = Zend_Registry::get('db');
        if (is_numeric($leadId)){
            $this->_leadId = $leadId;
        }
        parent::__construct();
    }

    public function init()
    {
        $db = $this->db;

        $leadId = $this->_leadId;
        $lead = new Core_Model_Lead($leadId);
        $lead = $lead->fetch();    


        $this->setAction("/lead/convert/lead_id/$leadId");
        $this->setMethod('post');
        /**
         * To account
         * Account shipping and billing address   
         */
        $toAccount = $this->createElement('checkbox', 'to_account')
                    ->setLabel('Or Create New Account');
       
        $accountName = $this->createElement('text', 'account_name')
                        ->setLabel('Account Name')
                        ->setValue($lead['company_name'])
                        ->setAllowEmpty(false)
                        ->addValidator(new Core_Model_Lead_Validate_ConvertLeadToAccountRequiredFields);
       
        $accountId = new Zend_Dojo_Form_Element_FilteringSelect('account_id');
        $accountId->setLabel('Select an existing account')
                ->setAutoComplete(true)
                ->setStoreId('existingAccountStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/account/jsonstore'))
                ->setAttrib("searchAttr", "account_name")
                ->addValidator(new Zend_Validate_Digits())
                ->setRequired(false);
 
        $accountBillingAddressLine1 = $this->createElement('text', 'account_billing_address_line_1')
                                        ->setLabel('Billing Address Line 1')
                                        ->setValue($lead['address_line_1']);

        $accountBillingAddressLine2 = $this->createElement('text', 'account_billing_address_line_2')
                                        ->setLabel('Billing Address Line 2')
                                        ->setValue($lead['address_line_2']);

        $accountBillingAddressLine3 = $this->createElement('text', 'account_billing_address_line_3')
                                        ->setLabel('Billing Address Line 3')
                                        ->setValue($lead['address_line_3']);

        $accountBillingAddressLine4 = $this->createElement('text', 'account_billing_address_line_4')
                                        ->setLabel('Billing Address Line 4')
                                        ->setValue($lead['address_line_4']);


        $accountBillingCity = $this->createElement('text', 'account_billing_city')
                                ->setLabel('Billing City')
                                ->setValue($lead['city'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        $accountBillingState = $this->createElement('text', 'account_billing_state')
                                ->setLabel('Billing State')
                                ->setValue($lead['state'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        $accountBillingPostalCode = $this->createElement('text', 'account_billing_postal_code')
                                ->setLabel('Billing Postal Code')
                                ->setValue($lead['postal_code'])
                                ->addValidator(new Zend_Validate_StringLength(0, 20));

        $accountBillingCountry = $this->createElement('text', 'account_billing_country')
                                ->setLabel('Billing Country')
                                ->setValue($lead['country'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));
              
       
        $accountShippingAddressLine1 = $this->createElement('text', 'account_shipping_address_line_1')
                                        ->setLabel('Shipping Address Line 1')
                                        ->setValue($lead['address_line_1']);

        $accountShippingAddressLine2 = $this->createElement('text', 'account_shipping_address_line_2')
                                        ->setLabel('Shipping Address Line 2')
                                        ->setValue($lead['address_line_2']);

        $accountShippingAddressLine3 = $this->createElement('text', 'account_shipping_address_line_3')
                                        ->setLabel('Shipping Address Line 3')
                                        ->setValue($lead['address_line_3']);

        $accountShippingAddressLine4 = $this->createElement('text', 'account_shipping_address_line_4')
                                        ->setLabel('Shipping Address Line 4')
                                        ->setValue($lead['address_line_4']);

        $accountShippingCity = $this->createElement('text', 'account_shipping_city')
                                ->setLabel('Shipping City')
                                ->setValue($lead['city'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        $accountShippingState = $this->createElement('text', 'account_shipping_state')
                                ->setLabel('Shipping State')
                                ->setValue($lead['state'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        $accountShippingPostalCode = $this->createElement('text', 'account_shipping_postal_code')
                                ->setLabel('Shipping Postal Code')
                                ->setValue($lead['postal_code'])
                                ->addValidator(new Zend_Validate_StringLength(0, 20));


        $accountShippingCountry = $this->createElement('text', 'account_shipping_country')
                                ->setLabel('Shipping Country')
                                ->setValue($lead['country'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));
  
        /*
         * To contact
         * contact shipping and billing address   
         */
        $contactBillingAddressLine1 = $this->createElement('text', 'contact_billing_address_line_1')
                                        ->setLabel('Billing Address Line 1')
                                        ->setValue($lead['address_line_1']);

        $contactBillingAddressLine2 = $this->createElement('text', 'contact_billing_address_line_2')
                                        ->setLabel('Billing Address Line 2')
                                        ->setValue($lead['address_line_2']);

        $contactBillingAddressLine3 = $this->createElement('text', 'contact_billing_address_line_3')
                                        ->setLabel('Billing Address Line 3')
                                        ->setValue($lead['address_line_3']);

        $contactBillingAddressLine4 = $this->createElement('text', 'contact_billing_address_line_4')
                                        ->setLabel('Billing Address Line 4')
                                        ->setValue($lead['address_line_4']);

        $contactBillingCity = $this->createElement('text', 'contact_billing_city')
                                ->setLabel('Billing City')
                                ->setValue($lead['city'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        $contactBillingState = $this->createElement('text', 'contact_billing_state')
                                ->setLabel('Billing State')
                                ->setValue($lead['state'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        $contactBillingPostalCode = $this->createElement('text', 'contact_billing_postal_code')
                                ->setLabel('Billing Postal Code')
                                ->setValue($lead['postal_code'])
                                ->addValidator(new Zend_Validate_StringLength(0, 20));
     
        $contactBillingCountry = $this->createElement('text', 'contact_billing_country')
                                ->setLabel('Billing Country')
                                ->setValue($lead['country'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));
              

        $contactShippingAddressLine1 = $this->createElement('text', 'contact_shipping_address_line_1')
                                        ->setLabel('Shipping Address Line 1')
                                        ->setValue($lead['address_line_1']);

        $contactShippingAddressLine2 = $this->createElement('text', 'contact_shipping_address_line_2')
                                        ->setLabel('Shipping Address Line 2')
                                        ->setValue($lead['address_line_2']);

        $contactShippingAddressLine3 = $this->createElement('text', 'contact_shipping_address_line_3')
                                        ->setLabel('Shipping Address Line 3')
                                        ->setValue($lead['address_line_3']);

        $contactShippingAddressLine4 = $this->createElement('text', 'contact_shipping_address_line_4')
                                        ->setLabel('Shipping Address Line 4')
                                        ->setValue($lead['address_line_4']);

        $contactShippingCity = $this->createElement('text', 'contact_shipping_city')
                                ->setLabel('Shipping City')
                                ->setValue($lead['city'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        $contactShippingState = $this->createElement('text', 'contact_shipping_state')
                                ->setLabel('Shipping State')
                                ->setValue($lead['state'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        $contactShippingPostalCode = $this->createElement('text', 'contact_shipping_postal_code')
                                ->setLabel('Shipping Postal Code')
                                ->setValue($lead['postal_code'])
                                ->addValidator(new Zend_Validate_StringLength(0, 20));


        $contactShippingCountry = $this->createElement('text', 'contact_shipping_country')
                                ->setLabel('Shipping Country')
                                ->setValue($lead['country'])
                                ->addValidator(new Zend_Validate_StringLength(0, 100));

        /**
         * Opportunity fields
         */
        $toOpportunity = $this->createElement('checkbox', 'to_opportunity')
                    ->setLabel('To Opportunity')
                    ->addValidator(new Core_Model_Lead_Validate_ConvertLeadToOppurutnityAccount);
                    
        $opportunityName = $this->createElement('text', 'opportunity')
                            ->setLabel('Opportunity Name')
                            ->setAllowEmpty(false)
                            ->addValidator(new Core_Model_Lead_Validate_ConvertLeadToOpportunityRequiredFields);
                            
        $opportunityValue = $this->createElement('text', 'opportunity_value')
                            ->setLabel('Opportunity Value')
                            ->setAllowEmpty(false)
                            ->addValidator(new Core_Model_Lead_Validate_ConvertLeadToOpportunityRequiredFields);

        $expectedCloseDate = new Zend_Dojo_Form_Element_DateTextBox('expected_close_date');
        $expectedCloseDate->setLabel('Expected Close Date');
        $expectedCloseDate->setAllowEmpty(false);
        $expectedCloseDate->addValidator(new Core_Model_Lead_Validate_ConvertLeadToOpportunityRequiredFields);

        $expectedCloseDate->setFormatLength('long')
                          ->setRequired(false)
                          ->setInvalidMessage('Invalid date');

        $leadSource = $this->createElement('select', 'lead_source')
                                ->setLabel('Lead Source');

        $leadSource->addMultiOption("", "");
        $sql = "SELECT name, lead_source_id FROM lead_source";
        $result = $db->fetchAll($sql);

        foreach ($result as $row) {
                 $leadSource->addMultiOption($row->lead_source_id, $row->name);
        }
        
        $leadSource->setValue($lead['lead_source_id']);
        $salesStage = $this->createElement('select', 'sales_stage')
                                ->setLabel('Sales Stage');

        $salesStage->addMultiOption("", "");
        $sql = "SELECT name, sales_stage_id FROM sales_stage";
        $result = $db->fetchAll($sql);

        foreach ($result as $row) {
                 $salesStage->addMultiOption($row->sales_stage_id, $row->name);
        }

        $branchId = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
        $branchId->setLabel('Assign To Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branch_name")
                ->setValue($lead['branch_id'])
                ->setAllowEmpty(false)
                ->addValidator(new Core_Model_Lead_Validate_ConvertLeadToAccountRequiredFields);
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
        $assignedTo->setAllowEmpty(false);
        $assignedTo->addValidator(new Core_Model_Lead_Validate_ConvertLeadToAccountRequiredFields);


        $toContact = $this->createElement('checkbox', 'to_contact')
                        ->setLabel('To Contact')
                        ->setAttribs(array('disabled'=>'disabled', 'checked'=>'checked'));

        $submit = $this->createElement('submit', 'submit')
                        ->setAttrib('class', 'submit_button');

	$hash = $this->createElement('hash', 'no_csrf_lead_convert',
            array(
                'salt' => 'unique',
                'ignore' => true,
            )
        );

        $this->addElements(array($toAccount, $accountName, $accountId, $accountBillingAddressLine1, 
                $accountBillingAddressLine2,
                $accountBillingAddressLine3,$accountBillingAddressLine4, $accountBillingCity, $accountBillingState,
                $accountBillingPostalCode, $accountBillingCountry, $accountShippingAddressLine1, 
                $accountShippingAddressLine2, $accountShippingAddressLine3,$accountShippingAddressLine4,
                $accountShippingCity, $accountShippingState, $accountShippingPostalCode, $accountShippingCountry, 
                $toOpportunity, $assignedTo, $branchId,
                $opportunityName, $opportunityValue, $expectedCloseDate, $leadSource, $salesStage, $toContact, 
                $contactBillingAddressLine1, $contactBillingAddressLine2, $contactBillingAddressLine3,
                $contactBillingAddressLine4, $contactBillingCity, $contactBillingState, $contactBillingPostalCode,
                $contactBillingCountry, $contactShippingAddressLine1, $contactShippingAddressLine2, 
                $contactShippingAddressLine3,$contactShippingAddressLine4, $contactShippingCity,
                $contactShippingState, $contactShippingPostalCode, $contactShippingCountry, $submit,$hash));

        $this->addDisplayGroup(array('account_id', 'to_account', 'account_name', 'account_billing_address_line_1',
                'account_billing_address_line_2',
                'account_billing_address_line_3','account_billing_address_line_4', 'account_billing_city', 
                'account_billing_state',
                'account_billing_postal_code', 'account_billing_country', 'account_shipping_address_line_1', 
                'account_shipping_address_line_2', 'account_shipping_address_line_3','account_shipping_address_line_4',
                'account_shipping_city', 'account_shipping_state', 'account_shipping_postal_code', 
                'account_shipping_country', 'assigned_to', 'branch_id'), 'account');

        $this->addDisplayGroup(array('to_opportunity', 'opportunity', 'opportunity_value','expected_close_date', 
                'lead_source', 'sales_stage'), 'opportunity_group');

        $this->addDisplayGroup(array('to_contact', 'contact_billing_address_line_1', 'contact_billing_address_line_2', 
                'contact_billing_address_line_3', 'contact_billing_address_line_4', 'contact_billing_city', 
                'contact_billing_state', 'contact_billing_postal_code', 'contact_billing_country',
                'contact_shipping_address_line_1', 'contact_shipping_address_line_2', 'contact_shipping_address_line_3', 
                'contact_shipping_address_line_4', 'contact_shipping_city', 'contact_shipping_state', 
                'contact_shipping_postal_code',
                'contact_shipping_country'), 
                'to_contact_group');
    
        $this->addDisplayGroup(array('submit'), 'submit');


        return $this;

    }
}


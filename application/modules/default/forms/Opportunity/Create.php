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

class Core_Form_Opportunity_Create extends Zend_Form
{
    public function init() 
    {
        $currentUser = Zend_Registry::get('user');
        $opportunityName = $this->createElement('text', 'name')
                            ->addValidator(
                                new Zend_Validate_StringLength(0, 100)
                            )
                            ->setLabel('Opportunity Name')
                            ->setRequired(true);

        $amount = $this->createElement('text', 'amount');
        $amount->setLabel('Amount')
            ->setRequired(true);


        $expectedCloseDate = 
            new Zend_Dojo_Form_Element_DateTextBox('expected_close_date');
        $expectedCloseDate->setLabel('Expected Close Date');
        $expectedCloseDate->setFormatLength('long')
              ->setRequired(true)
              ->setInvalidMessage('Invalid date');


        $leadSourceId = $this->createElement('select', 'lead_source_id')
                                ->setLabel('Lead Source');

        $leadSourceElement = new Core_Form_Lead_Element_LeadSource;
        $leadSourceId = $leadSourceElement->getElement();

        $salesStageId = $this->createElement('select', 'sales_stage_id')
                                ->setLabel('Sales Stage');

        $salesStageModel = new Core_Model_SalesStage;
        $salesStageModelData = $salesStageModel->fetchAll();
        foreach ($salesStageModelData as $datum) {
            $salesStageId->addMultiOption($datum['sales_stage_id'], $datum['name']);
        }

        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description');
	    $description->addValidator(new Zend_Validate_StringLength(0, 250));
        $description->setAttribs(array(
            'cols' => '40',
            'rows'  => '5', 
        ));

        $customer = $this->createElement(
            'radio', 'customer_type', array(
                'label' =>  'Customer type',
                'required' => true,
            )
        );
        $customer->addValidator(new Core_Model_Opportunity_Validate_CustomerType());

        $customer->addMultiOptions(
            array(
                Core_Model_Opportunity::CUSTOMER_TYPE_ACCOUNT => 'Account', 
                Core_Model_Opportunity::CUSTOMER_TYPE_CONTACT =>'Contact'
            )
        );

        $accountId = new Core_Form_Account_Element_Account;
        $accountElement = $accountId->getElement();
        $accountElement->setStoreParams(array('url'=>'/account/jsonstore'));
        
        $contactId = new Core_Form_Contact_Element_Contact;
        $contactElement = $contactId->getElement();   

        $campaignElementContainer = new Core_Form_Campaign_Element_Campaign;
        $campaignId = $campaignElementContainer->getElement();
      
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Opportunity To User');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);
       
        $branch = new Core_Form_Branch_Element_Branch;
        $branchId = $branch->getElement();
        $branchId->setRequired(true);

    
        $submit = $this->createElement('submit', 'submit', array
            (
                'ignore' => true,
                'class' => 'submit_button'
            )
        );

	$hash = $this->createElement('hash', 'no_csrf_opportunity_create',
            array(
                'salt' => 'unique',
                'ignore' => true,
            )
        );

        $this->addElements(
            array(
                $opportunityName, $amount, 
                $expectedCloseDate, $leadSourceId,
                $salesStageId, $description, 
                $contactElement, 
                $customer, $accountElement, $assignedTo, 
                $campaignId, $branchId, $submit, $hash
            )
        );

        $basicGroup = $this->addDisplayGroup(
            array(
                'name', 
                'amount', 
                'expected_close_date', 
                'lead_source_id',
                'sales_stage_id', 
                'description'
            ), 'basic'
        );                                                                    

        $relationsGroup = $this->addDisplayGroup(
            array(
                'customer_type',
                'account_id',
                'contact_id', 
                'campaign_id', 
                'assigned_to', 
                'branch_id'
            ), 
            'relations'
        );

        $submitGroup = $this->addDisplayGroup(array('submit'), 'submit');

        $this->setElementFilters(array('StringTrim'));

    }

}

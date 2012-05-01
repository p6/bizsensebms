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

        $this->addElements(
            array(
                $opportunityName, $amount, 
                $expectedCloseDate, $leadSourceId,
                $salesStageId, $description, 
                $contactElement, 
                $customer, $accountElement, $assignedTo, 
                $campaignId, $branchId, $submit
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

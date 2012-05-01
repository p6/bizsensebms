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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Opportunity_Search extends Zend_Form
{
    public function init()
    {
        $this->setName('search');
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $leadSource = $this->createElement('multiselect', 'lead_source')
				->setAttrib('size', '5')
                ->setLabel('Lead Source');

        $sql = "SELECT name, lead_source_id FROM lead_source";
        $result = $db->fetchAll($sql);

        foreach ($result as $row) {
         $leadSource->addMultiOption($row->lead_source_id, $row->name);
        }

        $salesStageId = $this->createElement('multiselect', 'sales_stage_id')
								->setAttrib('size', '5')
                                ->setLabel('Sales Stage');

        $sql = "SELECT name, sales_stage_id FROM sales_stage";
        $result = $db->fetchAll($sql);

        foreach ($result as $row) {
            $salesStageId->addMultiOption($row->sales_stage_id, $row->name);
        }
 
        $name = $this->createElement('text', 'name');
        $name->setLabel('Name');

        $accountId = new Zend_Dojo_Form_Element_FilteringSelect('account_id');
        $accountId->setLabel('Referece To Account')
                ->setAutoComplete(true)
                ->setStoreId('accountStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/account/jsonstore'))
                ->setAttrib("searchAttr", "account_name")
                ->addValidator(new Zend_Validate_Int())
                ->setRequired(false);

        $contactId = new Zend_Dojo_Form_Element_FilteringSelect('contact_id');
        $contactId->setLabel('Referece To Contact')
                ->setAutoComplete(true)
                ->setStoreId('contactStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/contact/jsonstore'))
                ->setAttrib("searchAttr", "first_name")
                ->setRequired(false)
                ->addValidator(new Zend_Validate_Digits()); 
        
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Lead To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(false);
        
        $branchId = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
        $branchId->setLabel('Leads Of Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branch_name")
                ->setRequired(false);

        $submit = $this->createElement('submit', 'submit')
                        ->setAttrib('class', 'submit_button')
                        ->setLabel('Search');

        $this->addElements(array($leadSource, $salesStageId, $name, $accountId,
                $contactId, $assignedTo, $branchId, $submit));

    }

}


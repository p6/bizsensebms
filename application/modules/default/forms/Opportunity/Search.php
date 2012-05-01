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

        $this->addElements(
            array(
                $leadSource, $salesStageId, $name, $accountId,
                $contactId, $assignedTo, $branchId, $submit, 
            )
        );

    }

}


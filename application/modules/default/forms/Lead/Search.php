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
class Core_Form_Lead_Search extends Zend_Form
{

    public function init()
    {
        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');
    
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $this->setAction('/lead');
        $this->setMethod('get');
        $this->setName('search');


        $leadSourceId = $this->createElement('multiselect', 'lead_source_id')
				->setAttrib('size', '5')
                ->setLabel('Lead Source');
        
        $leadSource = new Core_Model_Lead_Source; 
        $result = $leadSource->fetchAll();

        foreach ($result as $row) {
         $leadSourceId->addMultiOption($row->lead_source_id, $row->name);
        }

        $leadStatusId = $this->createElement('multiselect', 'lead_status_id')
				->setAttrib('size', '5')
                ->setLabel('Lead Status');

        $leadStatus = new Core_Model_Lead_Status;
        $result = $leadStatus->fetchAll();

        foreach ($result as $row) {
            $leadStatusId->addMultiOption($row->lead_status_id, $row->name);
        }

        $name = $this->createElement('text', 'name');
        $name->setLabel('Name');

        $companyName = $this->createElement('text', 'companyName');
        $companyName->setLabel('Company Name');

        $city = $this->createElement('text', 'city');
        $city->setLabel('City');

        $state = $this->createElement('text', 'state');
        $state->setLabel('State');

        $country = $this->createElement('text', 'country');
        $country->setLabel('Country');
            
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Lead To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(false);
        
        $branchId = new Zend_Dojo_Form_Element_ComboBox('branch_id');
        $branchId->setLabel('Leads Of Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branch_name")
                ->setRequired(false);

        /*
         * Decorate branchId such that it apears
         * In a signle line in the group
         

        $branchId->setDecorators(array(
                'DijitElement',
                'Description',
                'Errors',
                array('Label'),
            ));
          */

        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button')
                        ->setLabel('Search');



        $this->addElements(
            array(
                $leadSourceId, $leadStatusId, $name, $companyName, 
                $city,
            )
        );

        if ($acl->isAllowed($user, 'view all leads')) {
            $this->addelements(array($assignedTo, $branchId));
        } elseif ($acl->isAllowed($user, 'view own branch leads')) {
            $this->addelements(array($assignedTo));
        }

        /*
         * Add decorator to assignTo such that
         * It appears in a single row
         

        $assignTo->setDecorators(array(
                'DijitElement',
                'Description',
                'Errors',
                array('Label'),
            ));
        */


        $this->addelements(array($submit));


       /* $nameGroup = $this->addDisplayGroup(array('name', 'companyName', 'city'), 'personal');
        
        $nameGroup->getDisplayGroup('personal')->setAttrib('class','search_fieldset_medium');
        $name->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
        ));

        $companyName->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
        ));

        $city->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
        ));

        $leadGroup = $this->addDisplayGroup(array('lead_source_id', 'lead_status_id'), 'lead');
       
        $leadGroup->getDisplayGroup('lead')->setAttrib('class','search_fieldset_medium');
        $leadSourceId->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
        ));

        $leadStatusId->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('Label'),
        ));*/
 
       /* if ($acl->isAllowed($user, 'view all leads')) {
            $assignmentGroup = $this->addDisplayGroup(array('assign_to', 'branch_id'), 'assignment');
         } elseif ($acl->isAllowed($user, 'view own branch leads')) {
            $assignmentGroup = $this->addDisplayGroup(array('assign_to'), 'assignment');
         }
		
        if ($acl->isAllowed($user, 'view all leads') or $acl->isAllowed($user, 'view own branch leads')) {
        	$assignmentGroup->getDisplayGroup('assignment')->setAttrib('class','search_fieldset_medium');
		}

        $this->addDisplayGroup(array('submit'), 'submit');*/
      
        $this->setElementFilters(array(new Zend_Filter_StringTrim()));
    }
}

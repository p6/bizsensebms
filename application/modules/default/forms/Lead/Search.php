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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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


        $this->addElements(array($leadSourceId, $leadStatusId, $name, $companyName, $city));

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

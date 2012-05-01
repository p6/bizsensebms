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

class Core_Form_Lead_Import extends Zend_Form
{
    /*
     * @return Zend_Form
     * Form to import leads
     */
    public function init()
    {

        $this->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');

        $leadSourceIdElement = new Core_Form_Lead_Element_LeadSource;
        $leadSourceId = $leadSourceIdElement->getElement();

        $leadStatusIdElement = new Core_Form_Lead_Element_LeadStatus;
        $leadStatusId = $leadStatusIdElement->getElement();


        $user = new Core_Model_User;
        $userData = $user->fetch(); 
        $userEmail = $userData->email;
        $userBranch = $userData->branch_name;
        
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Lead To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $assignedTo->setRequired(true);

        $element = new Core_Form_Branch_Element_Branch;
        $branchId = $element->getElement();

        $csvFile = $this->createElement('file', 'lead_import_csv');
        $csvFile->setLabel('CSV file:')
             ->setRequired(true)    
            ->setDestination(APPLICATION_PATH . '/data/')
            ->setDescription('Upload CSV file')
            ->addValidator('Count', false, 1)    
            ->addValidator('Size', false, 102400) 
            ->addValidator('Extension', false, 'csv'); 

        $submit = $this->createElement('submit', 'submit', array (
                            'class' => 'submit_button'
                        )
                    );
                     

        $this->addElements(array($leadSourceId, $leadStatusId, 
            $assignedTo, $branchId, $csvFile, $submit));

    
        $metaDataGroup = $this->addDisplayGroup(array('lead_source_id', 'lead_status_id', 'assigned_to', 'branch_id', 
            'description'), 'metaData');
        $metaDataGroup->getDisplayGroup('metaData')->setLegend('Lead Meta Data');

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


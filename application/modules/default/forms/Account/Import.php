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
class Core_Form_Account_Import extends Zend_Form
{
    /*
     * @return Zend_Form
     * Form to import accounts
     */
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');

        $user = new Core_Model_User;
        $userData = $user->fetch(); 
        $userEmail = $userData->email;
        $userBranch = $userData->branch_name;
        
        $assignedTo = new Zend_Dojo_Form_Element_FilteringSelect('assigned_to');
        $assignedTo->setLabel('Assign Lead To')
            ->setAutoComplete(true)
            ->setStoreId('stateStore')
            ->setStoreType('dojo.data.ItemFileReadStore')
            ->setStoreParams(array('url'=>'/lead/assignto'))
            ->setAttrib("searchAttr", "email")
            ->setAttrib('displayedValue', $userEmail)
            ->setRequired(true);

        $branchId = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
        $branchId->setLabel('Assign To Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/lead/assigntobranch'))
                ->setAttrib("searchAttr", "branch_name")
                ->setAttrib('displayedValue', $userBranch)
                ->setRequired(true);

        $csvFile = $this->createElement('file', 'account_import_csv');
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

        $this->addElements(array($csvFile, $submit));
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


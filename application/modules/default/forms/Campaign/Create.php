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

/**
 * Create branch office form
 */
class Core_Form_Campaign_Create extends Zend_Form
{
    public function init() 
    {
        $name = $this->createElement('text', 'name')
                                ->setRequired(true)
                                ->addValidator(new Zend_Validate_StringLength(1,200))
                                ->setLabel('Name');

        $description = $this->createElement('textarea', 'description', array
                            (
                                'label' => 'Description',
                                'attribs' => array(
                                'rows' => 5,
                                'cols' => 30,
                                ),
                                'validators' => 
                                    array(
                                        'validator' =>  (new Zend_Validate_StringLength(0,250))
                                )
                            ) 
                        );

        $startDate = new Zend_Dojo_Form_Element_DateTextBox('start_date');
        $startDate->setLabel('Start Date');
        $startDate->setRequired(true);

        $endDate = new Zend_Dojo_Form_Element_DateTextBox('end_date');
        $endDate->setLabel('End Date');
        $endDate->setFormatLength('short')
                ->setRequired(true)
                ->addValidator(new BV_Validate_DateCompare)
                ->setInvalidMessage('Invalid date');

        $user = Zend_Registry::get('user');
        $userData = $user->fetch(); 
        $userId = $userData->user_id;
        $userBranch = $userData->branch_id;

        $assignToContainer = new Core_Form_User_Element_AssignedTo;
        $assignTo = $assignToContainer->getElement();
        $assignTo->setRequired(true);
        $assignTo->setLabel('Assign To');
        $assignTo->setValue($userId);

        $branchIdContainer = new Core_Form_Branch_Element_Branch;
        $branchId = $branchIdContainer->getElement();
        $branchId->setRequired(true);
        $branchId->setLabel('Branch');
        $branchId->setValue($userBranch);
        
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');

        $this->addElements(array($name, $description, $startDate, $endDate, 
               $assignTo, $branchId, $submit)); 
    }
}    

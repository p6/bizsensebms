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
Class Core_Form_Finance_Ledger_Create extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'name', array
            (
                'label' => 'Name',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 250))
                     )
            )
        );

        $groupCategory = new Core_Form_Finance_Group_Element_Group;
        $groupCategoryElement = $groupCategory->getElement();
        $groupCategoryElement->setRequired(true);

        $this->addElement($groupCategoryElement);

        $this->addElement('text', 'opening_balance', array
            (
                'required' => true,
                'label' => 'Opening balance',
                'validators' => array (new Bare_Validate_IsNumeric),
            )
        );
    
        $this->addElement('radio', 'opening_balance_type', array
            (
                'label' => 'Balance type',
                'required' => true,
                'multiOptions' => array(
                    array('key'=>Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT, 'value'=>'Debit'),
                    array('key'=>Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_CREDIT, 'value'=>'Credit'),
                ),
            )
        );

        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );

    }
}


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
 * @category BizSense
 * @package  Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Form_Finance_Purchase_Create extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $purchaseDate = new Zend_Dojo_Form_Element_DateTextBox('date');
        $purchaseDate->setRequired(true)
                    ->setLabel('Purchase Date');
        $this->addElement($purchaseDate);
        
        $user = new Core_Model_User;
        $userData = $user->fetch();
        $branchElement = new Core_Form_Branch_Element_Branch;
        $branchId = $branchElement->getElement();
        $branchId->setAttrib('displayedValue', $userData->branch_name);
        $branchId->setRequired(true);
        
        $this->addElement($branchId);
        
        $vendorId = new Core_Form_Finance_Vendor_Element_Vendor;
        $vendorElement = $vendorId->getElement();
        $vendorElement->setRequired(true);
        $vendorElement->setStoreParams(array('url'=>'/finance/vendor/jsonstore'));
        $this->addElement($vendorElement);
        
        $this->addElement('text', 'discount_amount', array(
                'label' => 'Discount Amount',
                'value' => '0',
                'validators' => array(
                                     'validator' =>  (new Bare_Validate_IsNumeric)
                                    )
            )
        );
        
        $this->addElement('text', 'freight_amount', array(
                'label' => 'Freight Amount',
                'value' => '0',
                'validators' => array(
                                     'validator' =>  (new Bare_Validate_IsNumeric)
                                    )
            )
        );
        
        $this->addElement('textarea', 'notes', array(
                'label' => 'Purchase notes',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );

        $this->addElement('textarea', 'payment_terms', array(
                'label' => 'Payment terms',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );

        
        $this->addElement('submit', 'submit', 
            array(
                'ignore' => true,
                'class' => 'submit_button'
            )
        );
    }
}


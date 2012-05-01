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
Class Core_Form_Finance_Currency_Create extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $this->addElement('text', 'finance_currency_name', array(
                'label' => 'Currency Name',
                'required' => 'true',
                'description' => "Example : Rupees",
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'finance_currency_symbol', array(
                'label' => 'Currency Symbol',
                'required' => 'true',
                'description' => "Example : ' INR '",
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'finance_currency_Fraction_al_Currency', array(
                'label' => 'Fractional Currency',
                'required' => 'true',
                'description' => "Example : ' Paisa '",
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
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


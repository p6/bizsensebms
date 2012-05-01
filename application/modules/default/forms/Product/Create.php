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
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending 
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

class Core_Form_Product_Create extends Zend_Form
{
    public function init()
    {
        $this->addElement(
            'text', 'name', array(
            'label'     =>  'Name',
            'required'  =>  true,
            'validators' =>  array(
                array(
                    'validator' => 
                        new Zend_Validate_StringLength(2, 100)
                ),
            ),
        ));

         $this->addElement(
            'textarea', 'description', array(
                'label'     =>  'Description',
                'required'  =>  false,
                'attribs'   =>  array('cols'=>40, 'rows'=>5),
                'validators' =>  array(
                    array(
                        'validator' =>
                            new Zend_Validate_StringLength(2, 500)
                    ),
                ),
            )
        );

        $this->addElement(
            'checkbox', 'subscribable', array(
                'label' =>  'Subscribable',
                'description' => 'Customers can subscribe to this product',
                'required'      =>  true,    
            )
        );

        $this->addElement(
            'checkbox', 'taxable', array(
                'label' => 'Item is taxable',
            )
        );

        $taxTypeIdElement = new Core_Form_Tax_Element_Type;
        $taxTypeId = $taxTypeIdElement->getElement();
        $this->addElement($taxTypeId);

        $this->addElement(
            'text', 'buying_price', array(
                'label' => 'Buying price',
                'required' => true,
               'validators' => array(
                    array(
                        'validator' =>
                            new Bare_Validate_IsNumeric,
                    )
                )
            
            )
        );
        
        $this->addElement(
            'text', 'selling_price', array(
                'label' => 'Selling price',
                'required' => true,
                'validators' => array(
                   array(
                        'validator' =>
                            new Bare_Validate_IsNumeric,
                    )
                    
                )
            )
        );
        
        $this->addElement(
            'text', 'part_number', array(
                'label' => 'Part Number',
                'required' => false,
            )
        );

        $this->addElement(
            'text', 'model_number', array(
                'label' => 'Model Number',
                'required' => false,
                'validators' => array(
                    array(
                        'validator' =>
                            new Zend_Validate_Db_NoRecordExists('product', 'model_number'),
                    )
                )
            )
        );
        
        $this->addElement(
            'text', 'make', array(
                'label' => 'Make',
                'required' => false,
            )
        );
        
        $this->addElement(
            'checkbox', 'active', array(
                'label' =>  'Active',
                'required' =>  false,    
            )
        );


        $this->addElement('submit', 'submit', array(
                'label'=>'submit',
                'class' => 'submit_button'
               ));
    }
}

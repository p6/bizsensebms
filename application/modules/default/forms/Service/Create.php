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

class Core_Form_Service_Create extends Zend_Form
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
                        new Zend_Validate_Db_NoRecordExists(
                            'service_item', 'name'
                        )
                ),
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
                'description' => 'Customers can subscribe to this service',
                'required'      =>  true,    
            )
        );

        $this->addElement(
            'checkbox', 'taxable', array(
                'label' => 'Item is taxable',
                'required' => true,    
            )
        );

        $taxTypeIdElement = new Core_Form_Tax_Element_Type;
        $taxTypeId = $taxTypeIdElement->getElement();
        $this->addElement($taxTypeId);

        $this->addElement(
            'text', 'unit_price', array(
                'label' => 'Unit price',
                'required' => true,
                'validators' => array(
        /**            array(
                        'validator' =>
                            new Zend_Validate_Float,
                    )
                    **/
                )
            )
        );

        $this->addElement(
            'checkbox', 'active', array(
                'label' =>  'Active',
                'required' =>  true, 
                'class' => 'submit_button'   
            )
        );


        $this->addElement('submit', 'submit', array('label'=>'submit'));
    }
}

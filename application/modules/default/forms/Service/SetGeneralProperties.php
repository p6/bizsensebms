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
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Model_Service_Form_SetGeneralProperties extends Zend_Form
{
    /**
     * The service item id on which we are operating
     */
    protected $_service_item_id;

    public function __construct($serviceItemId = null)
    {
        $this->_service_item_id = $serviceItemId;
        parent::__construct();
    }

    public function init()
    {
        echo $this->_service_item_id;
        $this->addElement('text', 'unit_price', array(
            'label'     =>  'Unit price',
            'attribs'   =>  array('size'=>'5'),
            'required'  =>  true,
            'validators' =>  array(
                array('validator'   =>  new Zend_Validate_Digits()),
            ),
        ));

        $taxTypeElement = new Model_Tax_Form_Element_TaxType;
        $taxTypeId = $taxTypeElement->getElement();

        $this->addElement($taxTypeId);
        $this->addElement('submit', 'submit', array(
                'label' => 'submit',
                'class' => 'submit_button'
                ));
        
    }
}

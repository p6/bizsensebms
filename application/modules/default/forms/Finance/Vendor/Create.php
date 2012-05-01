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
Class Core_Form_Finance_Vendor_Create extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $this->addElement('text', 'name', array
            (
                'label' => 'Vendor Name',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 250))
                     )
            )
        );
        
        $this->addElement('radio', 'type', array
            (
                'label' => 'Type',
                'required' => true,
                'multiOptions' => array(
                    array('key'=> Core_Model_Finance_Vendor::VENDOR_TYPE_SUNDRY_CREDITOR, 
                                                 'value'=>'Sundry creditor'),
                    array('key'=>Core_Model_Finance_Vendor::VENDOR_TYPE_OTHER, 
                                                        'value'=>'Other'),
                ),
                'value' => Core_Model_Finance_Vendor::VENDOR_TYPE_SUNDRY_CREDITOR,
            )
        );
        
        $this->addElement('text', 'company_name', array
            (
                'label' => 'Company Name',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 250))
                     )
            )
        );
        
        $this->addElement('text', 'addresss_line_1', array
            (
                'label' => 'Address Line 1',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'addresss_line_2', array
            (
                'label' => 'Address Line 2',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'addresss_line_3', array
            (
                'label' => 'Address Line 3',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'addresss_line_4', array
            (
                'label' => 'Address Line 4',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'city', array
            (
                'label' => 'City ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'state', array
            (
                'label' => 'State ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'postal_code', array
            (
                'label' => 'Postal Code ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 20))
                     )
            )
        );
        
        $this->addElement('text', 'country', array
            (
                'label' => 'Country ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 100))
                     )
            )
        );
        
        $this->addElement('text', 'phone', array
            (
                'label' => 'Phone ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 20))
                     )
            )
        );
        
        $this->addElement('text', 'mobile', array
            (
                'label' => 'Mobile ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 20))
                     )
            )
        );
        
        $this->addElement('text', 'fax', array
            (
                'label' => 'Fax ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 20))
                     )
            )
        );
        
        $this->addElement('text', 'email', array
            (
                'label' => 'E-mail ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 320))
                     )
            )
        );
        
        $this->addElement('text', 'website', array
            (
                'label' => 'Website ',
                'required' => false,
                'validators' => 
                     array(
                       'validator' =>  (new Bare_Validate_Uri)
                     )
            )
        );
        
        $this->addElement('textarea', 'description', array
            (
                'label' => 'Description',
                'attribs' => array(
                          'rows' => 5,
                          'cols' => 30,
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


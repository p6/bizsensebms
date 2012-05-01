<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
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


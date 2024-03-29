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

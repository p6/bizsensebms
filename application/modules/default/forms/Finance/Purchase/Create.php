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


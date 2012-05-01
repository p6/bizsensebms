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

class Core_Form_Tax_EditTaxType extends Core_Form_Tax_AddTaxType
{
    protected $_taxTypeId;

    public function __construct($taxTypeId)
    {
        if (is_numeric($taxTypeId)) {
            $this->_taxTypeId = $taxTypeId;
        }
        parent::__construct();
    }

    public function init()
    {
        $this->addElement('text', 'name', array(
                'label'         =>  'Name',
                'attribs'       =>  array('size'=>'10'),
                'validators'    =>  array(
                    (array('validator'  =>  new Zend_Validate_StringLength(2, 30))),
                ),
                'required'      =>  true,
            )
        );
        
        $this->addElement('text', 'description', array(
                'label'         =>  'Description',
                'attribs'       =>  array('size'=>'20'),
                'validators'    =>  array(
                    (array('validator'  =>  new Zend_Validate_StringLength(2, 30))),
                ),
                'required'      =>  false,
            )
        );


        $percentage = new Zend_Form_Element_Text('percentage');
        $percentage->setLabel('Tax Percentage')
                    ->setRequired(true)
                    #->addValidator(new Zend_Validate_Float)
                    ->setAttrib('size', '5');      
        $this->addElement($percentage);
        
        $this->addElement('submit', 'submit', array (
                        'class' => 'submit_button'
                        )
        );
        
        $this->getElement('submit')->setLabel('Edit tax type');
        $taxType = new Core_Model_Tax_Type($this->_taxTypeId);    
        $this->populate((array) $taxType->fetch());
    }    
}



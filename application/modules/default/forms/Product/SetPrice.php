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

class Core_Form_Product_SetPrice extends BV_Model_Essential_Abstract 
{

    protected $_productId;

    function __construct($productId) {

        $this->_productId = $productId;
        parent::__construct();
    }

    /*
     * Returns form to set product price
     * @return Zend_Form
     */		
    public function getForm() {
				
        $this->_form = new Zend_Form;
        $action = "/product/setprice/productId/" . $this->_productId;
        $this->_form->setAction($action);

        $this->getBranchSubForms();         

        $submit = $this->_form->createElement('submit', 'submit');
        $this->_form->addElements(array($submit));
        return $this->_form;
    }

    /*
     * Create suboform for each branch 
     */
    public function getBranchSubForms()
    {
        $branchModel = new Core_Model_Branch;
        $branches = $branchModel->fetchAll();
        foreach ($branches as $branch) {
            $subForm = new Zend_Form_SubForm;

            $result = $this->db->fetchRow('SELECT * FROM product_price_branch WHERE branch_id = ?', $branch->branch_id);
            $sellingPrice = $subForm->createElement('text', 'selling_price')
                                ->setAttrib('size', 5)
                                #->addValidator(new Zend_Validate_Float())
                                ->addFilter('StringTrim')
                                ->addFilter('StripTags')
                                ->setLabel('Selling Price');
                     $costPrice = $subForm->createElement('text', 'cost_price')
                                ->setAttrib('size', 5)
                                ->addFilter('StringTrim')
                                ->addFilter('StripTags')
                                #->addValidator(new Zend_Validate_Float())
                                ->setLabel('Cost Price');
            $taxTypeId = new Zend_Dojo_Form_Element_FilteringSelect('tax_type_id');
            $taxTypeId->setLabel('Tax Type')
                    ->setAutoComplete(true)
                    ->setStoreId('taxTypeStore')
                    ->setStoreType('dojo.data.ItemFileReadStore')
                    ->setStoreParams(array('url'=>'/jsonstore/taxtype'))
                    ->setAttrib("searchAttr", "name")
                    ->addValidator(new Zend_Validate_Int())
                    ->setRequired(true);

            if (!empty($result)) {
                $sellingPrice->setValue($result->selling_price);
                $costPrice->setValue($result->cost_price);
                $taxTypeId->setValue($result->tax_type_id);
            }


            $subForm->addElements(array($costPrice, $sellingPrice, $taxTypeId));
            $subForm->setLegend($branch->branch_name);                

            $this->_form->addSubForm($subForm, 'branch_' . $branch->branch_id);
        }
    }
    
}

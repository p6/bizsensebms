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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/**
 * Set price for product
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

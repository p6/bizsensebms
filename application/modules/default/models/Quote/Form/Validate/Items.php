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
  * Quote items validator
  */
class Model_Quote_Form_Validate_Items
{
    
    public $db;

    public $numerOfItems = 0;
    
    protected $_invalidMessage = array();

    protected $_quoteId = null;

    public function __construct($quoteId = null)
    {
        $this->db = Zend_Registry::get('db');
        if (is_numeric($quoteId)) {
            $this->_quoteId = $quoteId;
        }
    }
    

    /*
     * Return the array of invalid messages
     */
    public function getInvalidMessages()
    {
        return $this->_invalidMessage;
    }

    /*
     * Returns true if the input submitted is valid
     */
    public function isValid()
    {

        if (isset($_POST['productId'])) {
            $productId = $_POST['productId'];
            $this->numberOfItems = sizeof($productId);
        }
        if (isset($_POST['unitPrice'])) {
            $unitPrice = $_POST['unitPrice'];
        }
        if (isset($_POST['quantity'])) {
            $quantity = $_POST['quantity'];
        }
        if (isset($_POST['taxTypeId'])){
            $taxTypeId = $_POST['taxTypeId'];
        }

        $itemsValid = true;
        
        if ( isset($this->numberOfItems) and ($this->numberOfItems > 0) ) {
            for ($i = 1; $i <= $this->numberOfItems; $i++ ){
                if (!is_numeric($productId[$i])){
                    $itemsValid = false;
                    $this->_invalidMessage[] = "Product $i you selected is invalid";
                }

                if (!is_numeric($unitPrice[$i])){
                    $itemsValid = false;
                    $this->_invalidMessage[] = "Unit price for product $i you entered is invalid";
                }

                if (!is_numeric($quantity[$i])){
                    $itemsValid = false;
                    $this->_invalidMessage[] = "Quantity for product $i you entered is invalid";
                }

                if (!is_numeric($taxTypeId[$i])){
                    $itemsValid = false;
                    $this->_invalidMessage[] = "Tax type for product $i you selected is invalid";
                }
            }

        } else {
            $itemsValid = false;
            $this->_invalidMessage[] = "There are no items";
        }

        return $itemsValid;
    }

    public function getJs()
    {
        $productHelper = new Model_Product_Helper;

        $js = false;    
        $json = '';
            $js = '';   
            $json .= '[';
            if ( isset($this->numberOfItems) and ($this->numberOfItems > 0) ) {
                for ($i = 1; $i <= $this->numberOfItems; $i++ ){
                    if ($i == 1) {
                        $json .= '{';
                    } else {
                        $json .= ',{';
                    }
                    $digitsFilter = new Zend_Filter_Digits();
        
                    $productIdFromPost = $_POST['productId'][$i];
                    $productIdFiltered = $digitsFilter->filter($productIdFromPost);
                    $productName = $productHelper->getProductNameFromId($productIdFromPost); 

                    $json .= '"productId": "' .  $productIdFiltered . '"';
                    $json .= ',"productName": ' .  '"'.$productName . '"';
                
                    $unitPriceFromPost =  $_POST['unitPrice'][$i];                
                    $unitPriceFiltered = $digitsFilter->filter($unitPriceFromPost);
                    $json .= ',"unitPrice": "' . $unitPriceFiltered . '"';

                    $quantityFromPost = $_POST['quantity'][$i]; 
                    $quantityFiltered = $digitsFilter->filter($quantityFromPost);
                    $json .= ',"quantity": "' . $quantityFiltered . '"';
                    
                    $taxTypeIdFromPost = $_POST['taxTypeId'][$i];
                    $taxTypeFiltered = $digitsFilter->filter($taxTypeIdFromPost);        
                    $taxTypeModel = new Model_Tax_Type;
                    $taxTypeName = $taxTypeModel->getTaxNameFromId($taxTypeFiltered);
                    $taxTypePercentage = $taxTypeModel->getTaxPercentageFromId($taxTypeFiltered);
                    $json .= ',"taxTypeId": "' . $taxTypeFiltered . '"';
                    $json .= ',"taxTypeName": ' . '"' . $taxTypeName . '"';
                    $json .= ',"taxPercentage": ' . '"' . $taxTypePercentage . '"';

                    $json .= '}';
                }   
             $json .= ']';
            }

        $js .= 'var returnedItems = ' . $json . '; ';
        $js .= ' var toRecreate = true;';    
        return $js;             
    }

    /*
     * Build the item rows data using values from current quote
     */
    public function getJsUsingExisting()
    {
        $js = false;    
        $json = '';
        $js = '';   
        
        $quote = new Model_Quote($this->_quoteId);
        $quoteItems = (array) $quote->fetchItems();
        $numberOfItems = count($quoteItems);
        
        $json .= '[';
        $i = 1;
        foreach ($quoteItems as $key=>$value ){
                  if ($i == 1) {
                $json .= '{';
            } else {
                $json .= ',{';
            }
            $i++;
            $digitsFilter = new Zend_Filter_Digits();
        
            $product = $value->product_id;
            $productIdFiltered = $digitsFilter->filter($product);
            $productHelper = new Model_Product_Helper;
            $productName = $productHelper->getProductNameFromId($product); 

            $json .= '"productId": "' .  $productIdFiltered . '"';
            $json .= ',"productName": ' .  '"'.$productName . '"';
            $unitPrice =  $value->unit_price;
            $unitPriceFiltered = $digitsFilter->filter($unitPrice);
            $json .= ',"unitPrice": "' . $unitPriceFiltered . '"';

        

            $quantity = $value->quantity;
            $quantityFiltered = $digitsFilter->filter($quantity);
            $json .= ',"quantity": "' . $quantityFiltered . '"';

                   
            $taxTypeId = $value->tax_type_id;
            $taxTypeFiltered = $digitsFilter->filter($taxTypeId);        
            $taxTypeModel = new Model_Tax_Type;
            $taxTypeName = $taxTypeModel->getTaxNameFromId($taxTypeFiltered);
            $taxTypePercentage = $taxTypeModel->getTaxPercentageFromId($taxTypeFiltered);
            $json .= ',"taxTypeId": "' . $taxTypeFiltered . '"';
            $json .= ',"taxTypeName": ' . '"' . $taxTypeName . '"';
            $json .= ',"taxPercentage": ' . '"' . $taxTypePercentage . '"';

            $json .= '}';
        }   
        $json .= ']';

        $js .= 'var returnedItems = ' . $json . '; ';
        $js .= ' var toRecreate = true;';

        return $js;             
    
    }

}

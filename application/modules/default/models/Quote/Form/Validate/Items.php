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

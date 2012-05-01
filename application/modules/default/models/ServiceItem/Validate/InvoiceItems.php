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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_ServiceItem_Validate_InvoiceItems extends Zend_Validate_Abstract
{

    const ITEM = 'item';    

    /**
     * @see Zend_Validate_Abstract::_messageTemplates
     */
    protected $_messageTemplates = array(
        self::ITEM => "'%value%' is left blank"
    );
    
    /**
     * The array containing all the error messages 
     */ 
    protected $_allItemsMessages  = array();

    /**
     * Whether or not the validation from the last isValid call failed
     */
    protected $_validationStatus = true;

    /**
     * Array after the submmited rowset is filtered to retain sequence
     */
    protected $_filteredItemsValue = array();


    /**
     * @see Zend_Validate_Abstract::isValid()
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        $this->filterRemovedRows();
        $this->validateItemsRowset();
        return $this->_validationStatus;

    }

    /**
     * @return void
     */
    public function filterRemovedRows()
    {

        /**
         * service_item_id, quantity and unit_price are the array key of $value
         * Each element in the array $value is an array
         * Unfiltered array sample structure
         * $unfilteredArray = array(
         *   'product_id' =>   array('12', '4', '3'),
         *   'item_description' =>   array('12', '4', '3'),
         *   'quantity'  =>   array('1', '4', '3'),
         *   'unit_price' =>   array('100', '400', '300'),
         *   'tax_type_id' => array('2', '1', '3'),
         * )
         * Filtered array sample structure
         *  $filteredArray  = array(
         *       array('product_id'=>'1', 'item_description', 'quantity'=>'2', 'unit_price'=>'200', 'tax_type_id'=>'1'),
         *       array('product_id'=>'3', 'item_description', 'quantity'=>'1', 'unit_price'=>'300', 'tax_type_id'=>'2'),
         *   )
         */

        $filtered = array();

        if ( (!is_array($this->_value))) {
            return;
        } 

        if (!isset($this->_value['product_id'])) {
            return;
        }

        $productItemsSubmitted = $this->_value['product_id'];
        $itemDescriptionsSubmitted = $this->_value['item_description'];
        $unitPriceSubmitted = $this->_value['unit_price'];
        $taxTypeIdsSubmitted = $this->_value['tax_type_id'];
        $totalRowsSubmitted = count($productItemsSubmitted);

    
        foreach ($productItemsSubmitted as $key=>$value){
            $filtered[] = array(
                'product_id'    =>  $value, 
                'item_description'    =>  $itemDescriptionsSubmitted[$key], 
                'unit_price'     =>  $unitPriceSubmitted[$key],
                'tax_type_id'     =>  $taxTypeIdsSubmitted[$key],
            );
        }
        $this->_filteredItemsValue = $filtered;    

    }

    /**
     * @return bool
     */
    public function validateItemsRowset()
    {
        
        $isValid = true;
        $uniqueFilteredItemsValue = array();
        /**
         * Let us first check if there are any items submitted
         * If none we return false and set an error message
         */
        if (!count($this->_filteredItemsValue)) {
            $isValid = false;
            $this->_allItemsMessages[] = "No items submitted. The invoice must contain at least one item.";
            $this->_validationStatus = $isValid;    
            return $isValid;
        }


        /**
         * If the user removes a row of items randomnly, the array sequence will be lost
         * We make sure we work on only those rows that are actually submitted
         */
        foreach ($this->_filteredItemsValue as $key=>$value) {
            if (!is_numeric($value['product_id'])) {
                $isValid = false;
                $this->_allItemsMessages[] = "Name for item " .  ($key+1) . " is invalid";
            }

            if (!is_numeric($value['unit_price'])) {
                $isValid = false;
                $this->_allItemsMessages[] = "Unit price for item " .  ($key+1) . " is invalid";
            }
            
            if (!(in_array($value['product_id'], $uniqueFilteredItemsValue))) {
                $uniqueFilteredItemsValue[] = $value['product_id'];
            }
            else {
                $isValid = false;
                $this->_allItemsMessages[] = "Name for item " .  ($key+1) . " already exists";
            }

        }
        $this->_validationStatus = $isValid;    
        return $isValid;

 
    }

    /**
     * @return array the error messages
     */
    public function getAllItemsMessages()
    {
        return $this->_allItemsMessages;
    }

    /**
     * @return array filtered items
     */
    public function getFilteredItems()
    {
        return $this->_filteredItemsValue;
    }

    /**
     * @return array of filtered products
     */
    public function getFilteredJSON()
    {
       $this->_filteredJSON =  json_encode($this->_filteredItemsValue); 
       return $this->_filteredJSON;
    }
}


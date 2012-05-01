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
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending 
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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. \
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Form_Product_Edit extends Core_Form_Product_Create
{
    /**
     * The product item id on which we are operating
     */
    protected $_product_item_id;

    /**
     * @param int $product_item_id
     */
    public function __construct($product_item_id)
    {
        $this->_product_item_id = $product_item_id;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        
        $productModel = new Core_Model_Product($this->_product_item_id);
        $productDetails = $productModel->fetch()->toArray();
        $this->addElement(
            'text', 'model_number', array(
                'label' => 'Model Number',
                'required' => false,
                'validators' => array(
                    array(
                        'validator' =>
                            new Zend_Validate_Db_NoRecordExists(
                            'product', 'model_number',array(
                         'field' => 'model_number', 
                         'value' => $productDetails['model_number'] ))  
                    )
                )
            )
        );
        $this->populate($productDetails);
    }
}

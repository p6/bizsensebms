<?php
/*
 * Register items
 *
 *
 * LICENSE: GNU GPL V3
 *
 * This source file is subject to the GNU GPL V3 license that is bundled
 * with this package in the file license
 * It is also available through the world-wide-web at this URL:
 * http://bizsense.binaryvibes.co.in/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryvibes.co.in so we can send you a copy immediately.
 *
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @license    http://bizsense.binaryvibes.co.in/license   
 * @version    $Id:$
 */
class Inventory_Form_RegisterItems
{
    public $db;
    public $purchaseId;
    public $productId;

    public function __construct($purchaseId, $productId)
    {
        $this->db = Zend_Registry::get('db');
        $this->purchaseId = $purchaseId;
        $this->productId = $productId;
    }
    
    public function getForm()
    {
        $form = new Zend_Form;
        $purchaseId = $this->purchaseId;
        $productId = $this->productId;
        $form->setAction("/inventory/registeritems/purchaseId/$purchaseId/productId/$productId")
                ->setMethod('post');
        $itemsForm = $this->getItemsSubForm();        

        
        $submit = $form->createElement('submit', 'submit');
        
        $form->addSubForm($itemsForm, 'items');               
        $form->addElements(array($submit));
    
        return $form;
    }

    public function getItemsSubForm()
    {
        $subForm =  new Zend_Form_SubForm;
        $subForm->setIsArray(false);

        
        $purchase = new Purchase($this->purchaseId);    
        $products = $purchase->getItemDetails();
        foreach ($products as $product){    
            $productId = $product->productId;
            $quantity = $product->quantity;
            $counter = 0;
            for ($i = 1; $i <= $quantity; $i++){
                $slNo = $subForm->createElement('text', "slNo[$i]")
                                    ->setLabel("Serial no for product $productId");
                $subForm->addElement($slNo);
                $element = $subForm->createElement('text', "boxNo[$i]")
                                    ->setLabel("Box no for product $productId");
                $subForm->addElement($element);
                
            }
        $totalItems = $i-1;
        $total = $subForm->createElement('hidden', 'totalItems')
                            ->setValue($totalItems)
                            ->setOrder(100);
        $subForm->addElement($total);
        }

        
        return $subForm;

    }
}



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



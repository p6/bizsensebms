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

class PurchaseController Extends Zend_Controller_Action
{
    protected $_model;
    protected $_purchaseModel;

    public function init()
    {
        $this->_model = new Core_Model_PurchaseReturn;
	    $this->_purchaseModel = new Core_Model_Finance_Purchase;
    }
    
    /**
     * Searchable, sortable list of purchases
     */    
    public function indexAction()
    {
           
    }

    /**
     * Create a purchase item
     * @development implementation under process
     */ 
    public function createAction()
    {
       $this->_helper->layout->setLayout('without-sidebar');
        $purchaseId = $this->_getParam('purchse_id');
                
        $this->_purchaseModel->setPurchaseId($purchaseId);
        $form = new Core_Form_PurchaseReturn_Create;
        $form->setAction(
            $this->_helper->url(
                'create',
                'purchasereturn',
                'default',
                array(
                    'purchase_id' => $purchaseId,
                )
            )
        );
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Product_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
               $purchaseReturnId =  $this->_model->create(
                       $itemsValidator->getFilteredItems(),
                       $form->getValues(), $purchaseId
                    );
                $this->_helper->FlashMessenger('Purchase return has been created');
                $this->_helper->redirector(
                    'viewdetails','purchasereturn', 'default', 
                    array('purchase_return_id'=> $purchaseReturnId)
                );
            } else {
                $this->view->itemMessages = $itemsValidator->getAllItemsMessages();
                $this->view->includeRecreateScript = true;
                $this->view->returnedItemsJSON =  $itemsValidator->getFilteredJSON();
                $form->populate($_POST);
            }
        } else {

            /**
             * When the request is not post, we want to populate the form
             * using the values stored in the database
             */
            $purchaseItems =  $this->_purchaseModel->getInvoiceItems();
            $date = new Zend_Date();
            $date->setTimestamp($purchaseItems['date']);
            $invoiceItems['date'] = $this->view->timestampToDojo($purchaseItems['date']); 
            $form->populate($purchaseItems);
            $items = $this->_invoiceModel->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }

    /**
     * View details of the purchase
     * @dev implementation under process
     */
    public function viewdetailsAction()
    {
        
    }
    
    /**
     * Edit a purchase item
     * @dev implementation under process
     */
    public function editAction()
    {
        
    }

}

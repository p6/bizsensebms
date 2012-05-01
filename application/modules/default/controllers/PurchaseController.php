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
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

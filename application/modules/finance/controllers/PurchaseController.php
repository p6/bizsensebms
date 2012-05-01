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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Finance_PurchaseController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Finance_Purchase
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    function init()
    {
        $this->_model = new Core_Model_Finance_Purchase;
    }

    /**
     * Browsable, sortable, searchable list of Finance Purchases
     */
    public function indexAction()
    {
       $form = new Core_Form_Finance_Purchase_Search;
       $form->populate($_GET);
       $this->view->form = $form;
       
        /**
         * Notify the view if the submit was hit on the search form
         */
        if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
        
        $paginator = $this->_model->getPaginator($form->getValues(), 
                                                      $this->_getParam('sort'));  
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }
    
    /**
     * Create a new Purchase
     *
     * @see Zend_Controller_Action
     */ 
    public function createAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $form = new Core_Form_Finance_Purchase_Create;
        $form->setAction(
            $this->_helper->url(
                'create',
                'purchase',
                'finance'
            )
        );

        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Product_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);  
             
            if ($itemsValid and $formValid) {
                $purchaseId = $this->_model->create(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('Purchase has been created');
                $this->_helper->redirector(
                    'index','purchase', 'finance', 
                    array('purchase_id'=>$purchaseId)
                );
            } else {
                $this->view->itemMessages = 
                                        $itemsValidator->getAllItemsMessages();
                $this->view->includeRecreateScript = true;
                $this->view->returnedItemsJSON =  
                                            $itemsValidator->getFilteredJSON();
                $form->populate($_POST);
            }
        }
        $this->view->form = $form;
    }
    
    /**
     * View the details of the purchase
     */
    public function viewdetailsAction()
    {
        $purchaseId = $this->_getParam('purchase_id');
        $purchaseModel = new Core_Model_Finance_Purchase($purchaseId);
        $this->view->purchaseId = $purchaseId;
        $this->view->purchaseRecord = $purchaseModel->fetch();
        $this->view->purchaseItems = $purchaseModel->getItems();
    }
    
    /**
     * Edit a purchase
     */
    public function editAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $purchaseId = $this->_getParam('purchase_id');
        $this->_model->setPurchaseId($purchaseId);
        $form = new Core_Form_Finance_Purchase_Create;
        $form->setAction(
            $this->_helper->url(
                'edit',
                'purchase',
                'finance',
                array(
                    'purchase_id' => $purchaseId,
                )
            )
        );
        $this->view->form = $form;
        if ($this->_request->isPost()){
            $itemsValidator = new Core_Model_Product_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
                $this->_model->edit(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('purchase has been edited');
                $this->_helper->redirector(
                    'viewdetails','purchase', 'finance', 
                    array('purchase_id'=>$purchaseId)
                );
            } else {
                $this->view->itemMessages = 
                                        $itemsValidator->getAllItemsMessages();
                $this->view->includeRecreateScript = true;
                $this->view->returnedItemsJSON =  
                                            $itemsValidator->getFilteredJSON();
                $form->populate($_POST);
            }
        } else {

            /**
             * When the request is not post, we want to populate the form
             * using the values stored in the database
             */
            $purchaseItems =  $this->_model->getPurchaseItems();
            $date = new Zend_Date();
            $date->setTimestamp($purchaseItems['date']);
            $purchaseItems['date'] = 
                          $this->view->timestampToDojo($purchaseItems['date']); 
            $form->populate($purchaseItems);
            $items = $this->_model->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }
    
    /**
     * Delete the purchase
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setPurchaseId($this->_getParam('purchase_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The purchase was successfully deleted'; 
        } else {
           $message = 'The purchase could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'purchase', 'finance');
    }

    /**
     * vocher entry
     */
    public function vocherentryAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $form = new Core_Form_Finance_Purchase_VocherEntry;
        $form->setAction(
            $this->_helper->url(
                'vocherentry',
                'purchase',
                'finance'
            )
        );
        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Finance_Purchase_Validate_PurchaseItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);  
             
            if ($itemsValid and $formValid) {
                $purchaseId = $this->_model->vocherEntry(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('Purchase has been created');
                $this->_helper->redirector(
                    'index','purchase', 'finance', 
                    array('purchase_id'=>$purchaseId)
                );
            } 
            
        }
        $this->view->form = $form;    
    }
    
    /**
     * voucher details of the purchase
     */
    public function voucherdetailsAction()
    {
        $purchaseId = $this->_getParam('purchase_id');
        $purchaseModel = new Core_Model_Finance_Purchase($purchaseId);
        $this->view->purchaseId = $purchaseId;
        $this->view->purchaseRecord = $purchaseModel->fetch();
        $this->view->purchaseItems = $purchaseModel->getVoucherItems();
    }
    
    /**
     * Edit a purchase
     */
    public function editvoucherAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $purchaseId = $this->_getParam('purchase_id');
        $this->_model->setPurchaseId($purchaseId);
        $form = new Core_Form_Finance_Purchase_Create;
        $form->setAction(
            $this->_helper->url(
                'editvoucher',
                'purchase',
                'finance',
                array(
                    'purchase_id' => $purchaseId,
                )
            )
        );
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Finance_Purchase_Validate_PurchaseItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);  
             
            if ($itemsValid and $formValid) {
                $purchaseId = $this->_model->voucherEdit(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('Purchase has been created');
                $this->_helper->redirector(
                    'index','purchase', 'finance', 
                    array('purchase_id'=>$purchaseId)
                );
            } 
            
        } else {
            /**
             * When the request is not post, we want to populate the form
             * using the values stored in the database
             */
            $purchaseItems =  $this->_model->getPurchaseItems();
            $date = new Zend_Date();
            $date->setTimestamp($purchaseItems['date']);
            $purchaseItems['date'] = 
                          $this->view->timestampToDojo($purchaseItems['date']); 
            $form->populate($purchaseItems);
            $items = $this->_model->getVoucherItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }

}

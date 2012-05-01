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
 * You can contact Binary Vibes Information Technologies Pvt. 
 * Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
class PurchasereturnController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_PurchaseReturn
     */
    protected $_model;
    
    /**
     * @var object Core_Model_Finance_Purchase
     */
    protected $_purchaseModel;
    
    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_PurchaseReturn;
	    $this->_purchaseModel = new  Core_Model_Finance_Purchase;
    }
    
    /**
     * Browsable, sortable, searchable list of SalesReturn
     */
    public function indexAction()
    {
       $form = new Core_Form_PurchaseReturn_Search;
       $form->populate($_GET);
       $this->view->form = $form;
      
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
     * Create a new PurchaseReturn
     *
     * @see Zend_Controller_Action
     */ 
    public function createAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $purchaseId = $this->_getParam('purchase_id');
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
        if ($this->_request->isPost()){
            $itemsValidator = new Core_Model_Product_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
               $purchaseReturnId = $this->_model->create(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues(),$purchaseId
                    );
                $this->_helper->FlashMessenger(
                                          'purchase return has been created');
                $this->_helper->redirector(
                    'viewdetails','purchasereturn', 'default', 
                    array('purchase_return_id'=> $purchaseReturnId)
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
            $purchaseItems =  $this->_purchaseModel->getPurchaseItems();
            $date = new Zend_Date();
            $date->setTimestamp($purchaseItems['date']);
            $purchaseItems['date'] = 
                    $this->view->timestampToDojo($purchaseItems['date']); 
            $form->populate($purchaseItems);
            $items = $this->_purchaseModel->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }
    
     /**
     * View the details of PurchaseReturn
     */
    public function viewdetailsAction()
    {
        $purchaseReturnId = $this->_getParam('purchase_return_id'); 
        $this->_model->setpurchaseReturnId($purchaseReturnId);
        $this->view->purchaseReturn = $this->_model->fetch();
        $this->view->purchaseReturnItems = $this->_model->getItems();
        $this->view->purchaseReturnId = $purchaseReturnId;
    }
    
     /**
     * Edit a purchaseReturn
     */
    public function editAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $purchaseReturnId = $this->_getParam('purchase_return_id');
        $this->_model->setPurchaseReturnId($purchaseReturnId);
        $form = new Core_Form_PurchaseReturn_Create;
        $form->setAction(
            $this->_helper->url(
                'edit',
                'purchasereturn',
                'default',
                array(
                    'purchase_return_id' => $purchaseReturnId,
                )
            )
        );
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Product_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
                $this->_model->edit(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger(
                                    'Purchase Return has been edited');
                $this->_helper->redirector(
                    'viewdetails','purchasereturn', 'default', 
                    array('purchase_return_id'=> $purchaseReturnId)
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
            $purchaseReturnItems =  $this->_model->fetch();
            $date = new Zend_Date();
            $date->setTimestamp($purchaseReturnItems['date']);
            $purchaseReturnItems['date'] = 
                    $this->view->timestampToDojo($purchaseReturnItems['date']); 
            $form->populate($purchaseReturnItems);
            $items = $this->_model->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }

    /**
     * Delete the PurchaseReturn
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setPurchaseReturnId($this->_getParam('purchase_return_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The purchase return was successfully deleted'; 
        } else {
           $message = 'The purchase return could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'purchaseReturn', 'default');
        
    }
    
} 

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

/**
 * Product items crud operations
 */
class ProductController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Product
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_Product;
    }

    
    /**
     * List the products with sort, search and pagination
     */
    public function indexAction() 
    {
        $form = new Core_Form_Product_Search;
        $form->populate($_GET);
        $this->view->form = $form;
        
        $paginator = $this->_model->getPaginator('',$this->_getParam('sort'));  

        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
	
    } 

    /**
     * Create a product item
     */
    public function createAction()
    {
	    $form  = new Core_Form_Product_Create;
        $action = $this->_helper->url('create', 'product', 'default');
        $form->setAction($action);

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $this->_model->create($form->getValues());
	 	        $this->_helper->FlashMessenger('Product added successfully');
                $this->_helper->Redirector('index','product','default');
             } else {
                $form->populate($_POST);
             }
        } 
        $this->view->form = $form;
   }	

    /**
     * Edit product item details
     */
    public function editAction()
    {
        $productId = $this->_getParam('product_id'); 

        $this->_model->setId($productId);
        $productModel = $this->_model;        

	    $form  = new Core_Form_Product_Edit($productId);
        $form->setAction($this->view->url(array(
                'module'            =>  'default',
                'controller'        =>  'product',
                'action'            =>  'edit',
                'product_id'   =>  $productId,
            ), NULL, TRUE
        ));

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $productModel->edit($form->getValues());
	 	        $this->_helper->FlashMessenger('Product item edited successfully');
                $this->_redirect($this->view->url(array(
                    'module'        =>  'default',
                    'controller'    =>  'product',
                    'action'        =>  'viewdetails',
                    'product_id'    =>  $productId,
                ), null, false));
             } else {
                $form->populate($_POST);
                $this->view->form = $form;
             }

          } else {
	        $this->view->form = $form;
          }

    }	

    /**
     * View the details of the product item
     */
    public function viewdetailsAction()
    {
        $productId = $this->_getParam('product_id');
        $product = $this->_model->setId($productId);
        $this->view->product = $product->fetch();     
    
    }   

    /**
     * Delete a product
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setId($this->_getParam('product_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The product was successfully deleted'; 
        } else {
           $message = 'The product could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'product', 'default');

    }

    /**
     * Set price and tax properties for general product items
     */
    public function setgeneralAction()
    {
        $productId = $this->_getParam('product_id'); 

        $this->_model->setId($productId);
        $productModel = $this->_model;        

        $form  = new Core_Form_Product_SetGeneralProperties($productId);
        $form->setAction($this->view->url(array(
                'module'            =>  'default',
                'controller'        =>  'product',
                'action'            =>  'setgeneral',
                'product_id'   =>  $productId,
            )
        ));

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $productModel->setGeneralProperties($form->getValues());
                $this->_helper->FlashMessenger('Product item properties set successfully');
                $this->_redirect($this->view->url(array(
                    'module'        =>  'default',
                    'controller'    =>  'product',
                    'action'        =>  'viewdetails',
                    'product_id'    =>  $productId,
                ), null, false));
             } else {
                $form->populate($_POST);
                $this->view->form = $form;
             }

          } else {
            $this->view->form = $form;
          }

     
    }

    /**
     * Set price and tax properties for general product items
     */
    public function setsubscribableAction()
    {
        
    }


    /**
     * Send Zend_Dojo_Data of list of product items
     * @return void
     */
    public function jsonstoreAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $items = $this->_model->fetchAllActiveProducts();
        $data = new Zend_Dojo_Data('product_id', $items);
        $data->setLabel('name');
        $this->_helper->autoCompleteDojo($data);
    }


} 

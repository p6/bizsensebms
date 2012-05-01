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

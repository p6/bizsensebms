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

class SalesreturnController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_SalesReturn
     */
    protected $_model;
    
    /**
     * @var object Core_Model_Invoice
     */
    protected $_invoiceModel;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_SalesReturn;
	    $this->_invoiceModel = new Core_Model_Invoice;
    }
    
    /**
     * Browsable, sortable, searchable list of SalesReturn
     */
    public function indexAction()
    {
       $form = new Core_Form_SalesReturn_Search;
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
     * Create a new SalesReturn
     *
     * @see Zend_Controller_Action
     */ 
    public function createAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $invoiceId = $this->_getParam('invoice_id');
                
        $this->_invoiceModel->setInvoiceId($invoiceId);
        $form = new Core_Form_SalesReturn_Create;
        $form->setAction(
            $this->_helper->url(
                'create',
                'salesreturn',
                'default',
                array(
                    'invoice_id' => $invoiceId,
                )
            )
        );
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Product_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
                $salesReturnId = $this->_model->create(
                       $itemsValidator->getFilteredItems(),
                       $form->getValues(), $invoiceId
                    );
                $this->_helper->FlashMessenger('Sales return has been created');
                $this->_helper->redirector(
                    'viewdetails','salesreturn', 'default', 
                    array('sales_return_id'=> $salesReturnId)
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
            $invoiceItems =  $this->_invoiceModel->getInvoiceItems();
            $date = new Zend_Date();
            $date->setTimestamp($invoiceItems['date']);
            $invoiceItems['date'] =
                    $this->view->timestampToDojo($invoiceItems['date']); 
            $form->populate($invoiceItems);
            $items = $this->_invoiceModel->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }
    
    /**
     * View the details of SalesReturn
     */
    public function viewdetailsAction()
    {
        $salesReturnId = $this->_getParam('sales_return_id'); 
        $this->_model->setSalesReturnId($salesReturnId);
        $this->view->salesReturn = $this->_model->fetch();
        $this->view->salesReturnItems = $this->_model->getItems();
        $this->view->salesReturnId = $salesReturnId;
    }
    
    /**
     * Edit a SalesReturn
     */
    public function editAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $salesReturnId = $this->_getParam('sales_return_id');
        $this->_model->setSalesReturnId($salesReturnId);
        $form = new Core_Form_SalesReturn_Create;
        $form->setAction(
            $this->_helper->url(
                'edit',
                'salesreturn',
                'default',
                array(
                    'sales_return_id' => $salesReturnId,
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
                $this->_helper->FlashMessenger('Sales Return has been edited');
                $this->_helper->redirector(
                    'viewdetails','salesreturn', 'default', 
                    array('sales_return_id'=> $salesReturnId)
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
            $salesReturnItems =  $this->_model->fetch();
            $date = new Zend_Date();
            $date->setTimestamp($salesReturnItems['date']);
            $salesReturnItems['date'] = 
                     $this->view->timestampToDojo($salesReturnItems['date']); 
            $form->populate($salesReturnItems);
            $items = $this->_model->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }

    /**
     * Delete the Salesreturn
     */
    public function deleteAction()
    {
        $cForm = new BV_Form_Confirm;

        $salesReturnId = $this->_getParam('sales_return_id');
        $form = $cForm->getForm();
        $form->setAction($this->view->url(array(
                'module'            =>  'default',
                'controller'        =>  'salesreturn',
                'action'            =>  'delete',
                'sales_return_id'   =>  $salesReturnId,
            ), NULL, TRUE
        ));

        $this->_model->setSalesReturnId($salesReturnId);

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST) and $this->_getParam('yes') == 'Yes') {
                $deleted = $this->_model->delete();
                if ($deleted) {
                    $this->_helper->FlashMessenger('Sales Return deleted');
                } else {
                    $this->_helper->FlashMessenger(
                                      'Sales Return could not be deleted');
                }
                $this->_helper->redirector('index', 'salesreturn', 'default');

            } else {
                $this->_helper->redirector(
                    'viewdetails', 'salesReturn', 'default', 
                    array('sales_return_id' => $salesReturnId)
                );
            }
        } else {
            $salesReturnRecord = $this->_model->fetch();
            $this->view->service = $salesReturnRecord;
            $this->view->form = $form;
        }
        
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setSalesReturnId($this->_getParam('sales_return_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The sales return was successfully deleted'; 
        } else {
           $message = 'The sales return could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'salesreturn', 'default');
    }
    
    /**
     * Export the Sales Return to PDF document
     */
    public function exportAction()
    {
        $salesReturnId = $this->_getParam('salesReturn_id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->_model->setSalesReturnId($salesReturnId);
        $fileName = $this->_model->getPdfFileLocation();
        $file = file_get_contents($fileName);
       
        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="salesReturn.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file); 
    }

} 

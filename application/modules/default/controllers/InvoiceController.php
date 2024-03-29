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

class InvoiceController extends Zend_Controller_Action 
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Invoice;
    }

    /**
     * Create a service invoice
     */
    public function createAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $form = new Core_Form_Invoice_Create;
        $form->setAction(
            $this->_helper->url(
                'create',
                'invoice',
                'default'
            )
        );

        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Product_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
                $invoiceId = $this->_model->create(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('Invoice has been created');
                $this->_helper->redirector(
                    'viewdetails','invoice', 'default', 
                    array('invoice_id'=>$invoiceId)
                );
            } else {
                $this->view->itemMessages = $itemsValidator->getAllItemsMessages();
                $this->view->includeRecreateScript = true;
                $this->view->returnedItemsJSON =  $itemsValidator->getFilteredJSON();
                $form->populate($_POST);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Browsable, sortable, searchable list of service invoices
     */
    public function indexAction()
    {
       $form = new  Core_Form_Invoice_Search;
       $form->populate($_GET);
       $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
        
       $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));  
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
 
    }


    /**
     * View the details of the invoice
     */
    public function viewdetailsAction()
    {
        $invoiceId = $this->_getParam('invoice_id');
        $invoiceModel = new Core_Model_Invoice($invoiceId);
        $this->view->invoiceId = $invoiceId;
        $this->view->invoiceData = $invoiceModel->fetch();
        $this->view->invoiceItems = $invoiceModel->getItems();
    }

    /**
     * Delete the invoice
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setInvoiceId($this->_getParam('invoice_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The invoice was successfully deleted'; 
        } else {
           $message = 'The invoice could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'invoice', 'default');
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $invoiceId = $this->_getParam('invoice_id');
        $this->_model->setInvoiceId($invoiceId);
        $form = new Core_Form_Invoice_Create;
        $form->setAction(
            $this->_helper->url(
                'edit',
                'invoice',
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
                $this->_model->edit(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('Invoice has been edited');
                $this->_helper->redirector(
                    'viewdetails','invoice', 'default', 
                    array('invoice_id'=>$invoiceId)
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
            $invoiceItems =  $this->_model->getInvoiceItems();
            $date = new Zend_Date();
            $date->setTimestamp($invoiceItems['date']);
            $invoiceItems['date'] = $this->view->timestampToDojo($invoiceItems['date']); 
            $form->populate($invoiceItems);
            $items = $this->_model->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }

    /**
     * Export the invoice to PDF document
     */
    public function exportAction()
    {
        $invoiceId = $this->_getParam('invoice_id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_model->setInvoiceId($invoiceId);
        $fileName = $this->_model->getPdfFileLocation();
        $file = file_get_contents($fileName);
       
        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="invoice.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file); 
    }

    /**
     * Export the service invoice to PDF document
     */
    public function exportservicepdfAction()
    {
        $invoiceId = $this->_getParam('invoice_id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_model->setInvoiceId($invoiceId);
        $fileName = $this->_model->getServicePdfFileLocation();
        $file = file_get_contents($fileName);
       
        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="service.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file); 
    }


    /**
     * Invoice settings
     */
    public function settingsAction()
    {
        $form = new Core_Form_Invoice_Settings();
        $invoiceSettings = $this->_model->getSettings();
        $this->view->form = $form;
        $defaults = array(
            'prefix' => $invoiceSettings->getPrefix(),
            'suffix' => $invoiceSettings->getSuffix()
        );
        $form->populate($defaults);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $invoiceSettings->setPrefix($form->getValue('prefix'));     
                $invoiceSettings->setSuffix($form->getValue('suffix'));     
            } else {
                $form->populate($this->getRequest()->getPost());
            }
            $this->view->message = 'Invoice settings have been saved successfully';
            $this->_helper->FlashMessenger('Invoice settings have been saved successfully');
            $this->_helper->redirector('index', 'invoice', 'default');
        }
    }
    
    
    /**
     * Create a service invoice
     */
    public function createserviceinvoiceAction()
    {
       $this->_helper->layout->setLayout('without_sidebar');
       $form = new Core_Form_Invoice_CreateServiceInvoice;
       $form->setAction($this->_helper->url(
                'createserviceinvoice', 
                'invoice', 
                'default'
              ));
       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
            $itemsValidator = new Core_Model_ServiceProduct_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);  
            if ($itemsValid and $formValid) {
                $invoiceId = $this->_model->createServiceInvoice(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('Invoice has been created');
                $this->_helper->redirector(
                    'serviceinvoicedetails','invoice', 'default', 
                    array('invoice_id'=>$invoiceId)
                );
            } else {
                $this->view->itemMessages = $itemsValidator->getAllItemsMessages();
                $this->view->includeRecreateScript = true;
                $this->view->returnedItemsJSON =  $itemsValidator->getFilteredJSON();
                $form->populate($_POST);
            }
       }              
    }

    /**
     * View the details of the service invoice
     */
    public function serviceinvoicedetailsAction()
    {
        $invoiceId = $this->_getParam('invoice_id');
        $invoiceModel = new Core_Model_Invoice($invoiceId);
        $this->view->invoiceId = $invoiceId;
        $this->view->invoiceData = $invoiceModel->fetch();
        $this->view->invoiceItems = $invoiceModel->getServiceItems();
    }
    
    public function editserviceinvoiceAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $invoiceId = $this->_getParam('invoice_id');
        $this->_model->setInvoiceId($invoiceId);
        $form = new Core_Form_Invoice_CreateServiceInvoice;
        $form->setAction(
            $this->_helper->url(
                'editserviceinvoice',
                'invoice',
                'default',
                array(
                    'invoice_id' => $invoiceId,
                )
            )
        );
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_ServiceProduct_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
                $this->_model->editServiceItems(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('Invoice has been edited');
                $this->_helper->redirector(
                    'serviceinvoicedetails','invoice', 'default', 
                    array('invoice_id'=>$invoiceId)
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
            $invoiceItems =  $this->_model->getInvoiceItems();
            $date = new Zend_Date();
            $date->setTimestamp($invoiceItems['date']);
            $invoiceItems['date'] = $this->view->timestampToDojo($invoiceItems['date']); 
            $form->populate($invoiceItems);
            $items = $this->_model->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }
} 

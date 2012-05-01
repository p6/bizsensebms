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

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

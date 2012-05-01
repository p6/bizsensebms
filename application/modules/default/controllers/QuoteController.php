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

class QuoteController Extends Zend_Controller_Action
{
    public $db;
    
    protected $_model;

    public function init()
    {
        $this->db = Zend_Registry::get('db');
        $this->uid = Core_Model_User_Current::getId();
        $this->_model = new Core_Model_Quote;
    }
    
    /**
     * List of quotes
     * Search, browse, paginate, sort
     */
    public function indexAction()
    {
       $paginator = $this->_model->getPaginator('', $this->_getParam('sort'));  
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;

    } 
    
    /**
     * Create a quote
     */
    public function createAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $form = new Core_Form_Quote_Create;
        $form->setAction(
            $this->_helper->url(
                'create',
                'quote',
                'default'
            )
        );

        if ($this->_request->isPost()) {
            $itemsValidator = new Core_Model_Product_Validate_InvoiceItems();
            $itemsValid = $itemsValidator->isValid($_POST);
            $formValid =  $form->isValid($_POST);   
            if ($itemsValid and $formValid) {
                $quoteId = $this->_model->create(
                        $itemsValidator->getFilteredItems(),
                        $form->getValues()
                    );
                $this->_helper->FlashMessenger('Quote has been created');
                $this->_helper->redirector(
                    'index','quote', 'default', 
                    array('quote_id'=>$quoteId)
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
     * Edit a quote
     */
    public function editAction()
    {
        $this->_helper->layout->setLayout('without_sidebar');
        $quoteId = $this->_getParam('quote_id');
        $this->_model->setQuoteId($quoteId);
        $form = new Core_Form_Quote_Create;
        $form->setAction(
            $this->_helper->url(
                'edit',
                'quote',
                'default',
                array(
                    'quote_id' => $quoteId,
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
                $this->_helper->FlashMessenger('Quote has been edited');
                $this->_helper->redirector(
                    'viewdetails','quote', 'default', 
                    array('quote_id'=>$quoteId)
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
            $quoteItems =  $this->_model->fetch();
            if ($quoteItems['to_type'] == 1) {
                 $quoteItems['account_id'] = $quoteItems['to_type_id'];   
            }
            $date = new Zend_Date();
            $date->setTimestamp($quoteItems['date']);
            $quoteItems['date'] = $this->view->timestampToDojo($quoteItems['date']); 
            $form->populate($quoteItems);
            $items = $this->_model->getItemsJson();
            $this->view->returnedItemsJSON =  Zend_Json::encode($items);
        }
    }

    /**
     * View the details of the quote 
     * display form to send quote email
     */    
    public function viewdetailsAction()
    {
        $quote = new Core_Model_Quote($this->_getParam('quote_id'));
        $this->view->quote = $quote->fetch();
        $this->view->quoteItems = $quote->getItems();

        $quoteId = $this->_getParam('quote_id');
        $form = new Core_Form_Quote_Mail();
        $form->setAction(
            $this->_helper->url(
                'viewdetails',
                'quote',
                'default',
                array(
                    'quote_id' => $quoteId,
                )
            )
        );
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) { 
                $values = $this->getRequest()->getPost();
                $subject = $values['subject'];
                $textBody = $values['body'];
                $fetchedData = $quote->fetch();
                $quotePartyEmailAddressHelper = new BV_View_Helper_QuotePartyEmail;
                $quotePartyEmailAddress = $quotePartyEmailAddressHelper->quotePartyEmail(
                $fetchedData['to_type'], $fetchedData['to_type_id']);
                $this->_model->setQuoteId($quoteId);
                $success = $this->_model
                    ->sendQuoteEmail($quotePartyEmailAddress, $subject, $textBody);
                if ($success !== true) {
                    $this->getResponse()->setHttpResponseCode(403);
                    $this->_helper->FlashMessenger('Email could not sent');
                    $this->_helper->redirector('index','quote', 'default');
                } else {
                    $this->_helper->FlashMessenger('Email has been sent');
                    $this->_helper->redirector('index','quote', 'default');
                }
            }else {
                $form->populate($_POST);
                $this->view->form = $form;               
            }
        } else {
            $this->view->form = $form;
        }   
    }
  
    /**
     * Export the quote to PDF format
     */ 
    public function exportAction()
    {
        $quoteId =  $this->_getParam('quote_id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
       
        $this->_model->setQuoteId($quoteId);
        $fileName = $this->_model->getPdfFileLocation();
        $file = file_get_contents($fileName);

        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="quote.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file); 
    }
      
    /**
     * Delete a quote
     */ 
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setQuoteId($this->_getParam('quote_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The quote was successfully deleted'; 
        } else {
           $message = 'The quote could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'quote', 'default');

    }
    
    /**
     * sales register CSV export
     */
    public function csvexportAction()
    {
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       
       $quoteId = $this->_getParam('quote_id');
       $this->_model->setQuoteId($quoteId);
       $file = $this->_model->csvexport();
       
       
       $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 
                            'attachment; filename=Quote.csv')
                            ->appendBody($file);
    }
}

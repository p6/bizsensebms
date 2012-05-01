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

class Finance_VendorController extends Zend_Controller_Action 
{

    /**
     * @var object Core_Model_Finance_Vendor
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    function init()
    {
        $this->_model = new Core_Model_Finance_Vendor;
    }

    /**
     * Browsable, sortable, searchable list of Finance Vendors
     */
    public function indexAction()
    {
       $form = new Core_Form_Finance_Vendor_Search;
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
     * Create a new Vendor 
     *
     * @see Zend_Controller_Action
     */ 
    public function createAction()
    {
        $form = new Core_Form_Finance_Vendor_Create;
        $form->setAction($this->_helper->url('create', 'vendor', 'finance'));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->create($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                      'The vendor was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * Edit a Vendor Details
     */
    public function editAction()
    {
        $vendorId = $this->_getParam('vendor_id'); 
        $this->_model->setvendorId($vendorId);
  
        $form = new Core_Form_Finance_Vendor_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'vendor', 
                'finance',
                array(
                    'vendor_id'=>$vendorId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger(
                                'The vendor has been edited successfully');
                $this->_helper->redirector('index', 'vendor', 'finance',
                    array('vendor_id'=>$vendorId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }
    
    /**
     * Delete the Vendor
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setVendorId($this->_getParam('vendor_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The vendor was successfully deleted'; 
        } else {
           $message = 'The vendor could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'vendor', 'finance');      
        
    }
    
    /**
     * View the details of the Vendor
     */
    public function viewdetailsAction()
    {
        $vendorId = $this->_getParam('vendor_id');
        $vendorModel = new Core_Model_Finance_Vendor($vendorId);
        $this->view->vendorId = $vendorId;
        $this->view->vendorData = $vendorModel->fetch();
    }
    
    /**
     * Stores list of Vendor Ids for DOJO Dropdown button
     */
    public function jsonstoreAction()
    {
        $items = (array) $this->_model->fetchByType(
                    Core_Model_Finance_Vendor::VENDOR_TYPE_SUNDRY_CREDITOR);
        $data = new Zend_Dojo_Data('vendor_id', $items);
        $this->_helper->AutoCompleteDojo($data);
    }
    
    /**
     * Stores list of Vendor Ids for DOJO Dropdown button
     */
    public function storeAction()
    {
        $items = (array) $this->_model->fetchByType(
                    Core_Model_Finance_Vendor::VENDOR_TYPE_OTHER);
        $data = new Zend_Dojo_Data('vendor_id', $items);
        $this->_helper->AutoCompleteDojo($data);
    }
    
    /**
     * To Initialize new Ledger 
     */
    public function initializeledgerAction()
    {
        $vendorId = $this->_getParam('vendor_id'); 
        $this->_model->setvendorId($vendorId);
  
        $form = new Core_Form_Finance_Ledger_InitializeLedger;
        $form->setAction($this->_helper->url(
                'initializeledger', 
                'vendor', 
                'finance',
                array(
                    'vendor_id'=>$vendorId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->initializeLedger($form->getValues());
                $this->_helper->FlashMessenger(
                                 'The ledger initialized successfully');
                $this->_helper->redirector('index', 'vendor', 'finance',
                    array('vendor_id'=>$vendorId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }
 
} 

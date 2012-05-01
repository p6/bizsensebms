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
 * @category  BizSense
 * @package  Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version $Id:$
 */

class serviceproductController extends Zend_Controller_Action 
{
    
    protected $_model;

    public function init() 
    {
        $this->_model = new Core_Model_ServiceItem;
    } 
    
    /**
     * List the service product
     */
    public function indexAction() 
    {
        $paginator = $this->_model->getPaginator();  
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;

    } 

    /**
     * Create a service product
     */
    public function createAction()
    {
        $form = new Core_Form_ServiceItem_Create;
        $form->setAction($this->_helper->url('create', 'serviceproduct', 'default'));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->create($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                                   'The Service Product Item was created successfully');
                $this->_helper->Redirector('index');
            }
        }        
    }

    /**
     * View the detials of a service product
     */
    public function viewdetailsAction() 
    {
        $serviceProductId = $this->_getParam('service_item_id');
        $serviceProductModel = new Core_Model_ServiceItem($serviceProductId);
        $this->view->serviceItemId = $serviceProductId;
        $this->view->serviceItemRecord = $serviceProductModel->fetch();
    } 

    /**
     * Edit the service product details
     */
    public function editAction() 
    {
        $serviceProductId = $this->_getParam('service_item_id'); 
        $this->_model->setServiceProductId($serviceProductId);
        
        $form = new Core_Form_ServiceItem_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'serviceproduct', 
                'default',
                array(
                    'service_item_id'=>$serviceProductId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger(
                    'The Service Product has been edited successfully'
                );
                $this->_helper->redirector('index', 'serviceproduct', 'default',
                    array('service_item_id' => $serviceProductId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    
    } 

    /**
     * Delete the service product
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setServiceProductId($this->_getParam('service_item_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The service item was successfully deleted'; 
        } else {
           $message = 'The service item could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'serviceproduct', 'default');
    }

    /**
     * Send Zend_Dojo_Data of list of service product items
     * @return void
     */
    public function jsonstoreAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $items = $this->_model->fetchAllActiveItems();
        $data = new Zend_Dojo_Data('service_item_id', $items);
        $data->setLabel('name');
        $this->_helper->autoCompleteDojo($data);

    }

} 

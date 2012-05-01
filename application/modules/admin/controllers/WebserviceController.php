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
class Admin_WebserviceController extends Zend_Controller_Action 
{
    protected $_model;

    public function init() 
    {
        $this->_model = new Core_Model_WebService_Application;
    } 

    /**
     * @see Zend_Controller_Action
     * Browsable, sortable, searchable list of applications
     */
    public function indexAction()
    {
        $paginator = $this->_model->getPaginator('', $this->_getParam('sort'));  
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;
	
    }

    /**
     * Create a web service application
     * @see Zend_Controller_Action
     */
    public function createAction()
    {
        $form = new Core_Form_WebService_Application_Create;

        $action = $this->_helper->url(
            'create', 
            'webservice', 
            'admin'
        );
        $form->setAction($action);

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $applicationId = $this->_model->create($form->getValues());
                $status = $this->_model->getStatus();
                $this->_helper->FlashMessenger("Application created successfully");
                $this->_helper->redirector(
                    'index', 
                    'webservice', 
                    'admin', 
                    array('ws_application_id'=>$applicationId)
                );

            } else {
                $form->populate($_POST);

                $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
        }
                    
    }

    /**
     * View details of the application
     * @see Zend_Controller_Action
     */
    public function viewdetailsAction()
    {
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
        $wsApplicationId = $this->_getParam('ws_application_id');
        $model = new Core_Model_WebService_Application;
        $deleted = $model->setWsApplicationId($wsApplicationId)->delete();
        if ($deleted) {
            $this->_helper->FlashMessenger('The application was deleted');
        } else {
            $this->_helper->FlashMessenger('The application could not be deleted');
        }
        $this->_helper->Redirector('index', 'webservice', 'admin');
    }

    /**
     * Set the self service aplication URL
     */
    public function selfserviceappAction()
    {
        $form = new Core_Form_WebService_Application_SetSelfServiceApplication;

        $action = $this->_helper->url(
            'selfserviceapp', 
            'webservice', 
            'admin'
        );
        $form->setAction($action);

        $model = new Core_Model_WebService;
        $selfServiceUrl = $model->getSelfServiceUrl();
        $form->populate(array('url'=>$selfServiceUrl));
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $applicationId = $model->saveSelfServiceUrl($form->getValues());
                $status = $this->_model->getStatus();
                $this->_helper->FlashMessenger("Self service application information saved");
                $this->_helper->redirector(
                    'index', 
                    'webservice', 
                    'admin' 
                );
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }
        } else {
            $currentClientUrl = $model->getSelfServiceUrl();
            if (is_array($currentClientUrl)) {
                $form->populate($currentClientUrl);
            }
            $this->view->form = $form;
        }

    }

} 

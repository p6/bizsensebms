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

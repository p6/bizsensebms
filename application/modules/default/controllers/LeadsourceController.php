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

class LeadsourceController extends Zend_Controller_Action
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Lead_LeadSource;
    }

    /*
     * List all the lead source entries
     */
    public function indexAction()
    {
        $paginator = $this->_model->getPaginator(null, $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;
    }

    /*
     * Create a lead source item 
     */
    public function createAction()
    {
        $form = new Core_Form_Lead_AddSource;
        $form->setAction($this->_helper->url('create', 'leadsource', 'default'));
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('The lead source has been created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
   
    /**
     * Edit a lead source item
     */ 
    public function editAction()
    {
        $leadSourceId = $this->_getParam('lead_source_id');
        $form = new Core_Form_Lead_EditSource($leadSourceId);

        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $this->_model->setLeadSourceId($leadSourceId);
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The lead source has been edited successfully');
                $this->_helper->Redirector('index');
            } else {
               $form->populate($_POST);
               $this->view->form = $form;
            }
        } else {
           $form->populate((array) $this->_model->setLeadSourceId($this->_getParam('lead_source_id'))->fetch());
           $this->view->form = $form;
        }
    }
 
    /**
     * View lead source item  details
     */
    public function viewdetailsAction()
    {
        $this->view->leadSource = $this->_model->setLeadSourceId($this->_getParam('lead_source_id'))->fetch();
    }

    /**
     * Delete lead source
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setLeadSourceId($this->_getParam('lead_source_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The lead source was successfully deleted'; 
        } else {
           $message = 'The lead source could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'leadsource', 'default');
    }
}

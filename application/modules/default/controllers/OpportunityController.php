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

class OpportunityController extends Zend_Controller_Action 
{

    /**
     * Status constants
     */
    const SAVE_LEAD = '1';
    const SAVE_OPPORTUNITY = '2';
    const SAVE_ACCOUNT = '3';
    const SAVE_CONCACT = '4';

    /**
     * @var object opportunity model
     */
    protected $_model;

    /**
     * Initialize the model
     */
    public function init()
    {
        $this->_model = new Core_Model_Opportunity;
    }

    /**
     * List the opportunities
     */
    public function indexAction() 
    {
        $form = new Core_Form_Opportunity_Search;
        $form->populate($_POST);
        $this->view->form = $form;
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    } 

    /**
     * Edit an opportunity
     */
    public function editAction()
    {
	    $opportunityId = $this->_getParam('opportunity_id');
        $opportunity = new Core_Model_Opportunity($opportunityId);
    
	    $this->view->opportunityId = $opportunityId;

        $form = new Core_Form_Opportunity_Edit($opportunityId);
        $form->setAction(
            $this->_helper->url(
                'edit', 
                'opportunity', 
                'default', 
                array(
                    'opportunity_id' => $opportunityId
                )
            )
        );

        if ($this->_request->isPost()){
            if ($form->isValid($_POST)){
                $opportunity->edit($form->getValues());
			    $this->_helper->FlashMessenger('Opportunity edited');
		        $this->_redirect($this->view->url(array(
                    'module'            =>  'default',
                    'controller'         =>  'opportunity',
                    'action'            =>  'viewdetails',
                    'opportunity_id'    =>  $opportunityId,
                ), null, true));
         } else {
               $form->populate($_POST);

               $this->view->form = $form;
               }

         } else {
            $this->view->form = $form;
	     }


    } 			

    /**
     * Delete an opportunity
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        $deleted = $this->_model
                        ->setOpportunityId($this->_getParam('opportunity_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The opportunity was successfully deleted'; 
        } else {
           $message = 'The opportunity could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'opportunity', 'default');

    }


    /**
     * View details of an opportunity
     */
    public function viewdetailsAction() 
    {
	    $opportunityId = $this->_request->getParam('opportunity_id');
	    $this->view->opportunityId = $opportunityId;	

        $opportunity = new Core_Model_Opportunity($opportunityId);

	    $this->view->opportunity = $opportunity->fetch();
	
    } 

    /**
     * Create an opportunity
     */
    public function createAction() 
    {
        $form = new Core_Form_Opportunity_Create();
        $form->setAction(
                $this->_helper->url('create', 'opportunity', 'default')
            );

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)){
                $opportunity = new Core_Model_Opportunity;
                $opportunityId = $opportunity->create($form->getValues());
			    $this->_helper->FlashMessenger('Opportunity added successfully');
		        $this->_helper->redirector(
                    'viewdetails',
                    'opportunity',
                    'default',
                    array(
                        'opportunity_id' => $opportunityId,         
                    )
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
     * Browsable, sortable, searchable list of opportunity notes
     */
    public function notesAction()
    {
        $opportunityId = $this->_getParam('opportunity_id');
        $opportunity = new Core_Model_Opportunity;
        $opportunity->setOpportunityId($opportunityId);
        $this->view->opportunityId = $opportunityId;
        $notes =  $opportunity->getNotes();
        $paginator = $notes->getPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;

    }

    /**
     * Create a note to this opportunity
     */
    public function createnoteAction()
    {
        $opportunityId = $this->_getParam('opportunity_id');
        $this->view->opportunityId = $opportunityId;
        $this->_model->setOpportunityId($opportunityId);
        $model = $this->_model->getNotes();
        $form = new Core_Form_Account_Note_Create();
        $form->setAction($this->view->url(array(
                'module'        =>  'default',
                'controller'    =>  'opportunity',
                'action'        =>  'createnote',
                'opportunity_id'    =>  $opportunityId,
            ), NULL, TRUE));

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $opportunityNotesId = $model->create($form->getValues());
                $this->_helper->FlashMessenger('Opportunity note was created successfully');
                $url = $this->view->url(array(
                    'module'        =>  'default',
                    'controller'    =>  'opportunity',
                    'action'        =>  'notes',
                    'opportunity_id'    =>  $opportunityId,        
                ), null, true);
	 	        $this->_redirect($url);
		        $this->view->message = "Opportunity was created successfully";
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

         } else {
            $this->view->form = $form;
	     }

    }


    /**
     * Assign opportunity to a branch while creating and editing a lead
     * @output Zend_Dojo_Data for the autocomplete field
     */
    public function assigntobranchAction()
    {

        $data = $this->_model->getAssignedToBranchDojoData();
        $this->_helper->autoCompleteDojo($data);
    }

    public function accountAction()
    {   
        $accountId = $this->_request->getParam('account_id');
        $this->view->accountId = $accountId;
        $opportunity = $this->_model->accountOpportunity($accountId);
        $this->view->opportunity = $opportunity;
    }
    
    public function contactAction()
    {   
        $contactId = $this->_request->getParam('contact_id');
        $this->view->contactId = $contactId;
        $opportunity = $this->_model->contactOpportunity($contactId);
        $this->view->opportunity = $opportunity;
    }

} 

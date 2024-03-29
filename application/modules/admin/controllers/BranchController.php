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

class Admin_BranchController extends Zend_Controller_Action 
{
    
    protected $_model;

    public function init() 
    {
        $this->_model = new Core_Model_Branch;
    } 

    /**
     * List the branches
     */
    public function indexAction() 
    {
        $select = $this->_model->getListingSelectObject(null, $this->_getParam('sort'));
 
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator = $paginator;

    } 

    /**
     * Create a branch
     */
    public function createAction()
    {
        $bForm = new Core_Form_Branch_Create;
        $form = $bForm->getForm();
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $branchId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger("Branch created successfully");
                $this->_redirect("/admin/branch/viewdetails/branch_id/$branchId");
            } else {
               $form->populate($_POST);
               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        } 
             
    }

    /**
     * View the detials of a branch
     */
    public function viewdetailsAction() 
    {
        $branchId = $this->_getParam('branch_id');
		$branch = new Core_Model_Branch($branchId);
        $this->view->branch = $branch->fetch();
    } 

    /**
     * Edit the branch details
     */
    public function editAction() 
    {
        $branchId =  $this->_getParam('branch_id');

        $bForm = new Core_Form_Branch_Edit($branchId);
        $form = $bForm->getForm();
        $form->setAction($this->view->url(array(
                'module'        =>  'admin',
                'controller'    =>  'branch',
                'action'        =>  'edit',
                'branch_id'     =>  $branchId,
            )
        ));

        if ($this->_request->isPost()){
            if ($form->isValid($_POST)){
                $this->_model->setId($branchId);
                $this->_model->edit($form->getValues()); 
                $this->_helper->FlashMessenger("Branch edited successfully");
                $this->_redirect("/admin/branch/viewdetails/branch_id/$branchId");
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
	    }
    
    } 

    /**
     * Delete the branch
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setBranchtId($this->_getParam('branch_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The branch was successfully deleted'; 
        } else {
           $message = 'The branch could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'branch', 'admin');

    }


} 

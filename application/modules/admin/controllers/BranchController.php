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

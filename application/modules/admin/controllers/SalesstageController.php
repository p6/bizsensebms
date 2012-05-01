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
class Admin_SalesstageController extends Zend_Controller_Action
{
  
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_SalesStage;
    }
    
    public function indexAction()
    {
        $paginator = $this->_model->getPaginator(null, $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;

    }

    public function createAction()
    {
        $form = new Core_Form_SalesStage_Create;
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $salesStageId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('Sales stage successfully added');
                $this->_helper->Redirector('index', 'salesstage', 'admin');
            } 
        }
    }

    public function editAction()
    {
        $salesStageId = $this->_getParam('sales_stage_id');
        $this->_model->setSalesStageId($salesStageId);
        $form = new Core_Form_SalesStage_Edit($this->_model);
        $action = $this->_helper->Url('edit', 'salesstage', 'admin', array('sales_stage_id'=>$salesStageId));
        $form->setAction($action);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $salesStageId = $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('Sales stage successfully updated');
                $this->_helper->Redirector('index', 'salesstage', 'admin');
            } 
        }

    }

    public function deleteAction()
    {
        $salesStageId = $this->_getParam('sales_stage_id');
        $this->_model->setSalesStageId($salesStageId)->delete();
        $this->_helper->FlashMessenger('Sales Stage Deleted Successfully');
        $this->_helper->Redirector('index', 'salesstage', 'admin');
    }
}

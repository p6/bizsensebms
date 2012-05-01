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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
 
class Finance_TaxController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Tax_Type
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_Tax_Type;
    }

    /**
     * Browsable, sortable, searchable list of Tax Type
     */
    public function indexAction()
    {
    
    }
    
            
    public function classAction() 
    {
    }

    public function addclassAction()
    {
    }

    /**
     * Add a tax type
     */
    public function addtypeAction()
    {
        $form = new Core_Form_Tax_AddTaxType;
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger(
                                      'Tax type was successfully added');
                $this->_helper->Redirector('listtype', 'tax', 'finance');
            } 
        } 
    }

    /**
     * Sortable, searchable list of tax types
     */
    public function listtypeAction()
    {
        $tax = new Core_Model_Tax_Type;
        $select = $tax->getTaxTypes();
        $paginator = 
           new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

    }
    
    /**
     * Edit the tax type
     */
    public function edittypeAction()
    {
        $taxTypeId = $this->_getParam('tax_type_id');
        $this->view->taxTypeId = $taxTypeId;

        $form = new Core_Form_Tax_EditTaxType($taxTypeId);
        $form->setAction(
            $this->_helper->url(
                'edittype', 'tax', 'finance', 
                array('tax_type_id'=>$taxTypeId)
            ));
        $this->view->form = $form;
        if ($this->_request->isPost()){
            if ($form->isValid($_POST)){
                $taxType = new Core_Model_Tax_Type($taxTypeId);
                $taxType->edit($form->getValues());
                $this->_helper->FlashMessenger('Tax type edited successfully');
                $this->_helper->Redirector('listtype', 'tax', 'finance');
             } 
         } 
 
    }
 
    /*
     * Delete a tax type item
     */
    public function deletetypeAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setTaxTypeId($this->_getParam('tax_type_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The tax type was successfully deleted'; 
        } else {
           $message = 'The tax type could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('listtype', 'tax', 'finance');
                                                                                 
    }  
  
    public function viewtypedetailsAction()
    {
    }

    public function storeAction()
    {
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('tax_type_id', $items);
        $this->_helper->autoCompleteDojo($data);
    }

} 

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

/** Add, modify, remove, list salutation
 * Like Mr Ms Mr Mrs Dr Prof
 */

class Admin_SalutationController extends Zend_Controller_Action
{
    public $db;

    public function init()
    {
        $this->db = Zend_Registry::get('db');
    }

    /*
     * List the salutaions
     */
    public function indexAction()
    {
        $select = $this->db->select();
        $select->from(array('s'=>'salutation'), array('salutation_id', 'name', 'description'));
        $result = $this->db->fetchAll($select);
        $this->view->salutation = $result;
    }

    /*
     * Add a salutaion item
     */
    public function addAction()
    {
        $sForm = new BV_Form_FieldValue('/admin/salutation/add');
        
        $form = $sForm->getAddForm();

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
               
                $data = array(
                    'name'          =>  $this->_getParam('name'),
                    'description'   =>  $this->_getParam('description')
                ); 
                $this->db->insert('salutation', $data);
                $this->_redirect('/admin/salutation/');
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        } 
        
    }
   
    /**
     * Edit a salutation item
     */ 
    public function editAction()
    {
        $salutationId = $this->_getParam('salutation_id');

        $select = $this->db->select();
        $select->from(array('s'=>'salutation'), array('salutation_id', 'name', 'description'))
                        ->where('salutation_id = ?', $salutationId);
        $result = $this->db->fetchRow($select);
        
        $sForm = new BV_Form_FieldValue('/admin/salutation/edit/salutation_id/' . $salutationId);

        /*
         * If $result is false, avoid generating PHP warnings 
         */
        if (!empty($result)) {
            $name = $result->name; $description = $result->description;
            $sForm->setValues($name, $description);
            $form = $sForm->getAddForm();
        } else {
            $form = null;
        }

       
        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
               
                $data = array(
                    'name'          =>  $this->_getParam('name'),
                    'description'   =>  $this->_getParam('description')
                ); 
                
                $this->db->update('salutation', $data, 'salutation_id = '. $salutationId);
                $this->_redirect('/admin/salutation/');
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        } 
 


    }
 
    /**
     * Delete a salutation
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $salutationModel = new Core_Model_Salutation;   
        $deleted = $salutationModel
                        ->setSalutationId($this->_getParam('salutation_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The salutation was successfully deleted'; 
        } else {
           $message = 'The salutation could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'salutation', 'admin');

    }
}



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



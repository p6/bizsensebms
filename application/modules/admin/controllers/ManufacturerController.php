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

class Admin_ManufacturerController extends Zend_Controller_Action 
{
    public $db;

    public function init()
    {
        $this->db = Zend_Registry::get('db');
    }
       
    /**
     * Browsable, sortable, searchable list of manufacturers/sdf
     */ 
    public function indexAction() 
    {
        $select = Core_Model_Manufacturer_Util::getManufacturers();  

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));

        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

         
    }

    /**
     * Add a manufacturer
     */
    public function addAction()
    {
       $form = new Core_Form_Manufacturer_Create;
       
       if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {

                $data = array(
                    'name'              =>  $this->_getParam('name'),
                    'description'       =>  $this->_getParam('description'),
                ); 
                $this->db->insert('manufacturer', $data);
                $this->_helper->FlashMessenger('Manufacturer added');
                $this->_redirect('/admin/manufacturer');
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        }  
    }

    /**
     * Edit a manufactuerer
     */
    public function editAction()
    {
        $manufacturerId = $this->_getParam('manufacturer_id');
        $form = new Core_Form_Manufacturer_Edit($manufacturerId);
        $form->setAction($this->view->url(array(
                'module'            =>  'admin',
                'controller'        =>  'manufacturer',
                'action'            =>  'edit',
                'manufacturer_id'   =>  $manufacturerId,
            )
        ));
       
        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {

                $data = array(
                    'name'              =>  $this->_getParam('name'),
                    'description'       =>  $this->_getParam('description'),
                ); 
                $manufacturer = new Core_Model_Manufacturer($manufacturerId);
                $manufacturer->edit($form->getValues());
                $this->_helper->FlashMessenger('Manufacturer edited');
                $this->_redirect($this->view->url(array(
                        'module'        =>  'admin',
                        'controller'    =>  'manufacturer',
                        'action'        =>  'index',
                    ), null, true
                ));
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        }  
    }

      /**
     * Delete the manufacturer
     */
    public function deleteAction()
    {
        $cForm = new BV_Form_Confirm;

        $manufacturerId = $this->_getParam('manufacturer_id');
        $url = $this->view->url(array(
                    'module'        =>  'admin',
                    'controller'    =>  'manufacturer',
                    'action'        =>  'index',
                    )
               );

        $action = $url;
        $form = $cForm->getForm();

        $manufacturer = new Core_Model_Manufacturer($manufacturerId);

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST) and $this->_getParam('yes') == 'Yes') {

                $deleted = $manufacturer->delete();

                if ($deleted) {
                    $this->_helper->FlashMessenger('Manufacturer deleted');
                } else {
                    $this->_helper->FlashMessenger('Manufacturer could not be deleted');
                }
                $this->_redirect($this->view->url(array(
                    'module'=>'admin', 'controller'=>'manufacturer', 'action'=>'index'), null, true)
                );
            } else {
                $this->_redirect($url);
            }


        } else {
            $this->view->manufacturer = $manufacturer->fetch();
            $this->view->form = $form;
        }

    }


} 

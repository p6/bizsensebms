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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

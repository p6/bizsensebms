<?php 
/*
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

class Admin_PstatusController extends Zend_Controller_Action 
{
    public $db;

    protected $_model;

    public function init()
    {
        $this->db = Zend_Registry::get('db');
        $this->_model = new Core_Model_Product_Status;
    }
        
    public function indexAction() 
    {
        $paginator = $this->_model->getPaginator($search = null, $sort = null);

        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

         
    }

    public function addAction()
    {
        $form = new Core_Form_Product_Status_Create;

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $data = array(
                    'name'              =>  $this->_getParam('name'),
                    'description'       =>  $this->_getParam('description'),
                ); 
                $this->db->insert('product_status', $data);
                $this->_helper->FlashMessenger('Product status created');
                $this->_redirect($this->view->url(array(
                        'module'        =>  'admin',
                        'controller'    =>  'pstatus',
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

} 

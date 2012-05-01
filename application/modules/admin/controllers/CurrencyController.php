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
class Admin_CurrencyController extends Zend_Controller_Action 
{
    public $db;

    public function init()
    {
        $this->db = Zend_Registry::get('db');
    }
        
    /**
     * Display a list of currencies
     */
    public function indexAction() 
    {
        $db = Zend_Registry::get('db');
        $select = $db->select();
        $select->from(array('c'=>'currency'),
                    array('currency_id', 'name', 'symbol', 'description'));

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));

        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

         
    }

    /**
     * Add a currency to the database
     */
    public function addAction()
    {
       $form = new Core_Form_Currency_Create;
       
       if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                unset($data['submit']);    
                $this->db->insert('currency', $data);
                $this->_helper->FlashMessenger('Currency added');
                $this->_redirect('/admin/currency');
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        }  
    }

    /**
     * Edit the currency
     */
    public function editAction()
    {
        $currencyId = $this->_getParam('currency_id');
        $form = new Core_Form_Currency_Edit($currencyId);
        $form->setAction($this->view->url(array(
                'module'        =>  'admin',
                'controller'    =>  'currency',
                'action'        =>  'edit',
                'currency_id'   =>  $currencyId,
            ), null, true
        ));        
       
        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $currency = new Core_Model_Currency($currencyId);
                $currency->edit($form->getValues());
                $this->_helper->FlashMessenger('Currency has been edited');
                $this->_redirect($this->view->url(array(
                        'module'        =>  'admin',
                        'controller'    =>  'currency',
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
     * Delete the currency
     */
    public function deleteAction()
    {
        $cForm = new BV_Form_Confirm;

        $currencyId = $this->_getParam('currency_id');
        $url = $this->view->url(array(
                    'module'        =>  'admin',
                    'controller'    =>  'currency',
                    'action'        =>  'index',
                    )
               );

        $action = $url;
        $form = $cForm->getForm();
   
        $currency = new Core_Model_Currency($currencyId);
       
        if ($this->_request->isPost()) {

            if ($form->isValid($_POST) and $this->_getParam('yes') == 'Yes') {

                $deleted = $currency->delete();

                if ($deleted) {
                    $this->_helper->FlashMessenger('Currency deleted');
                } else {
                    $this->_helper->FlashMessenger('Currency could not be deleted');
                }    
                $this->_redirect($this->view->url(array(
                    'module'=>'admin', 'controller'=>'currency', 'action'=>'index'), null, true)
                );
            } else {
                $this->_redirect($url);
            }


        } else {
            $this->view->currency = $currency->fetch();      
            $this->view->form = $form;
        }        
       
    }



} 

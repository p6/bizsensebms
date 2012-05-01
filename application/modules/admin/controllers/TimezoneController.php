<?php 
/*
 * Timezone
 *
 *
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Admin_TimezoneController extends Zend_Controller_Action 
{
    public $db;    

    function init()
    {
        $this->db = Zend_Registry::get('db');
    }	

    public function indexAction() 
    {

    }


    public function setAction()
    {
        $tForm = new Core_Form_Timezone_Set;
        $form = $tForm->getForm();
        
        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $data = array(
                    'timezone'  =>  $this->_getParam('timezone'),
                );              

                $exists = $this->db->fetchOne('SELECT id FROM settings');
                if ($exists) {
                    $this->db->update('settings', $data);
                } else {
                    $this->db->insert('settings', $data);
                } 
                
                $this->_helper->FlashMessenger('Timezone has been changed successfully');
                $this->_redirect('/admin/timezone'); 
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        } 
    }
} 
<?php 
/*
 * Site information related settings
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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
 

class Admin_SiteinfoController extends Zend_Controller_Action 
{

    public $db;

    function init()
    {
        $this->db = Zend_Registry::get('db');
    }	

    public function indexAction() 
    {
               
    }

    public function footerAction()
    {
        $form = new Core_Form_SiteInfo_Footer;
        
        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $data = array(
                    'footer'    =>  $this->_getParam('footer'),
                );
                $this->db->update('organization_details', $data);               
                $this->view->message = "Footer successfully changed"; 
                $this->_helper->FlashMessenger('Footer set successfully');
                $this->_helper->Redirector->gotoSimple('index', 'index', 'admin');    
            } else {
               $form->populate($_POST);
            }

        } else {

        }         
           $this->view->form = $form;
    }    
    
    public function seturlAction() 
    {
        $form = new Core_Form_SiteInfo_SetURL;
        
        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $variableModel = new Core_Model_Variable;
                $data = $form->getValues();
                $variableModel->save('server_name',$data['site_url']);
                $this->_helper->FlashMessenger(
                             'The site URL has been changed successfully');
                $this->_helper->redirector('index', 'siteinfo', 'admin');    
            } else {
               $form->populate($_POST);
            }

        } else {
            $variableModel = new Core_Model_Variable('server_name');
            if ($variableModel->getValue() != '') {
                $defaultValues['site_url'] = $variableModel->getValue(); 
                $form->populate($defaultValues);
            }
        }         
           $this->view->form = $form;
    }

} 

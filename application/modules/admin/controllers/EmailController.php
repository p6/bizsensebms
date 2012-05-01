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
class Admin_EmailController extends Zend_Controller_Action 
{

    protected $_model;

    function init()
    {
        $this->_model = new Core_Model_Email;
    }	

    public function indexAction() 
    {

    }

    /*
     * Set outgoing email options
     */
    public function setAction()
    {
        $form = new Core_Form_Email_Set;
               
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $this->_model->configure($form->getValues());
                $this->view->message = "Email settings updated";    
            } else {
               $form->populate($_POST);
               $this->view->form = $form;
            }
        } else {
           $this->view->form = $form;
        }   
    }
    
    /*
     * test email configuration
     */
    public function testmailAction()
    {
        $form = new Core_Form_Email_TestMail;
               
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $result = $this->_model->testMail($form->getValues());
                
                if ($result) {
                    $this->_helper->FlashMessenger(
                        'An email has been sent to specified email address');
                }
                else {
                    $this->_helper->FlashMessenger(
                                    'Error in email configuration settings');
                }  
                $this->_redirect($this->view->url(array(
                    'module'        =>  'admin',
                    'controller'    =>  'email',
                    'action'        =>  'index'
                )));
            } else {
               $form->populate($_POST);
               $this->view->form = $form;
            }
        } else {
           $this->view->form = $form;
        }   
    }
    
} 

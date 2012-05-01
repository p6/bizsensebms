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

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

class Admin_StatusController extends Zend_Controller_Action 
{
    /**
     *@var string status model
     */
    protected $_model;

    /**
     *initalize the model 
     */
    public function init()
    {
        $this->_model = new Core_Model_Status;
    }
 
    public function indexAction() 
    {
        
    }

    /**
     * View log entries
     */
    public function viewlogAction()
    {
        $form = new Core_Form_Status_Log;
        $form->setAction($this->_helper->url('viewlog', 'status', 'admin'));
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $logItems = new Core_Model_Bizlog;
                $paginator = $logItems->getPaginator($form->getValues(), $this->_getParam('sort'));
                $paginator->setCurrentPageNumber($this->_getParam('page'));
                $paginator->setItemCountPerPage(25);
                $this->view->paginator = $paginator;
            }
        }else {
            $logItems = new Core_Model_Bizlog;
            $paginator = $logItems->getPaginator($this->_getParam('search'), $this->_getParam('sort'));
            $paginator->setCurrentPageNumber($this->_getParam('page'));
            $paginator->setItemCountPerPage(25);
            $this->view->paginator = $paginator;
        }
    }

    /**
     * delete all log entries
     */
    public function clearlogAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);  
        $bizlogModel = new Core_Model_Bizlog;
        $bizlogModel->deleteAllEntries();
        $this->_helper->FlashMessenger('Cleared log');
        $this->_helper->redirector('viewlog', 'status', 'admin');
    }
    
    /**
     * file permission
     */
     public function filepermissionAction()
     {
        $pathsToCheck = array(
            APPLICATION_PATH . '/configs',
            APPLICATION_PATH . '/data',
            APPLICATION_PATH . '/data/documents/image',
            APPLICATION_PATH . '/data/documents/invoice',
            APPLICATION_PATH . '/data/documents/receipt',
            APPLICATION_PATH . '/data/documents/reports',
            APPLICATION_PATH . '/data/documents/salesreturn',
            APPLICATION_PATH . '/data/quote',
            APPLICATION_PATH . '/data/quote/pdf',
            APPLICATION_PATH . '/data/logo',
            APPLICATION_PATH . '/data/log',
            PUBLIC_PATH . '/files/logo',
        );

        $filePermission = $this->_model->checkPermission($pathsToCheck); 
        $this->view->pathsToCheck = $pathsToCheck;
        $this->view->filePermission = $filePermission;
     }

    /**
     * cron status
     */ 
    public function cronAction() 
    {
        $variable = 'core_service_cron_lock';
        $variableModel = new Core_Model_Variable;
        $result = $variableModel->setVariable($variable)->getValue();
        $this->view->result = $result;
        if ($result) {
            $form = new Core_Form_Cron_Enable;
            $this->view->form = $form;
            $form->setAction($this->view->url(array(
                'module'        =>  'admin',
                'controller'    =>  'status',
                'action'        =>  'cron'
            ), null, true)); 

            $variableModel->save($variable,0);
            $this->_helper->FlashMessenger('Cron Lock Released Successfully');
        }
    }      

} 

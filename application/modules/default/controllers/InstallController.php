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

/*
 * We need to add application to our include path to make use of Install/Form/*
 */
set_include_path(APPLICATION_PATH . PATH_SEPARATOR . get_include_path() );

class InstallController extends Zend_Controller_Action 
{
    public function init() 
    {
        $this->_helper->layout->disableLayout();

    }
		
	
    public function indexAction() 
    {
        $installer = new Core_Service_Install_Process;

        $permissionsOk = $installer->checkFilePermissions();
        if (!$permissionsOk) {
            $this->view->messages = $installer->getMessages();
            return;
        }

        $access = $installer->checkAccess();
        if (!$access) {
            $this->_forward('access', 'error');
            return;
        }
        
        $form = new Core_Service_Install_Form_Create;
        if ($this->_request->isPost()){
            if ($form->isValid($_POST)){
                $installer->setInput($form->getValues());
                $installer->writeConfig();
                $installer->createTables();
                $installer->fillTables();
                $this->view->installed = true;
 
	        } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

       } else {
            $this->view->form = $form;	
	   }
    } 

} 

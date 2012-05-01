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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Webservice_ContactController extends Zend_Rest_Controller
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Contact;
        $this->_model->setAccessCheck(false);
        $this->_service = new Core_Service_WebService_Rest_Contact;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Create a lead 
     */
    public function postAction()
    {
    }

    public function indexAction() 
    {
    }

    public function getAction()
    {
        $result = '';
        $param = $this->_getParam('id');
        if ($param == 'byemail') {
            $result =  $this->_model->fetch(
                'byEmail', 
                array(
                    'work_email' => $this->_getParam('email')
                )
            );
        }
        if ($result) {
            $this->getResponse()->setHttpResponseCode(200);
            $this->_helper->json($result);
        } else {
            $this->getResponse()->setHttpResponseCode(204);
        }

    }

    public function  putAction()
    {
        $contactEmail = $this->_getParam('id');
        $this->_service->setWorkEmail($contactEmail);
        $data = Zend_Json::decode($this->getRequest()->getRawBody());
        $this->_service->updatePassword($data);
        $this->_helper->json((array) $this->_service->fetch());

    }
    
    public function deleteAction()
    {

    }

}

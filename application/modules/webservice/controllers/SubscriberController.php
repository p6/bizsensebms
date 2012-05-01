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
class Webservice_SubscriberController extends Zend_Rest_Controller
{
    protected $_service;

    public function init()
    {
        $this->_service = new Core_Service_WebService_Rest_Newsletter_Subscriber;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        $response = $this->getResponse();
        $result = $this->_service->create($post);
        if (is_numeric($result)) {
            $response->setHttpResponseCode(201);
            $this->_helper->json(array('subscriber_id' => $result));
        }
        else {
            $response->setHttpResponseCode(400);
            $this->_helper->json(array('error' => $result));
        }
    }

    public function indexAction() 
    {
        $collection = $this->_service->fetchAll();
        $this->_helper->json($collection);
    }

    public function getAction()
    {
        $subscriberId = $this->_getParam('subscriber_id');
        $response = $this->getResponse();
        $record = $this->_service->fetch($subscriberId);
        if ($record) {
            $response->setHttpResponseCode(200);
            $this->_helper->json($record);
        } else {
            $response->setHttpResponseCode(404);
        }
 
    }

    public function putAction()
    {
        $subscriberId = $this->_getParam('subscriber_id');
        $data = Zend_Json::decode($this->getRequest()->getRawBody());
        $response = $this->getResponse();
        $form = new Core_Form_Newsletter_Subscriber_Edit($subscriberId);
        if ($form->isValid($data)) {
            $edited = $this->_service->edit($data,$subscriberId);
            $response->setHttpResponseCode(200);
            $this->_helper->json($edited);
        } else {
            $response->setHttpResponseCode(400);
            $this->_helper->json($form->getMessages());
        }
    }

    
    public function deleteAction()
    {
        $subscriberId = $this->_getParam('subscriber_id');
             
        $deleted = $this->_service->delete($subscriberId);
        if ($deleted) {
            $responseCode = 204; 
        } else {
           $responseCode = 404; 
        }
        $this->getResponse()->setHttpResponseCode($responseCode);
    }

}

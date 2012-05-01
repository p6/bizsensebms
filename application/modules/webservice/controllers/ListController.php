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
class Webservice_ListController extends Zend_Rest_Controller
{
    protected $_service;

    public function init()
    {
        $this->_service = new Core_Service_WebService_Rest_Newsletter_List;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        $response = $this->getResponse();
        $form = new Core_Form_Newsletter_List_Create();
        if ($form->isValid($post)) {
            $created = $this->_service->create($post);
            $response->setHttpResponseCode(201);
            $this->_helper->json($created);
        } else {
            $response->setHttpResponseCode(400);
            $this->_helper->json($form->getMessages());
        }
    }

    public function indexAction() 
    {
        $collection = $this->_service->fetchAll();
        $this->_helper->json($collection);
    }

    public function getAction()
    {
        $listId = $this->_getParam('id');
        $this->_service->setListId($listId);
        $response = $this->getResponse();
        $data = $this->_service->fetch();
        if ($data) {
            $this->_helper->json($data);
        } else {
            $response->setHttpResponseCode(404);
        }

    }

    public function  putAction()
    {
        $listId = $this->_getParam('id');
        $this->_service->setListId($listId);
        $data = Zend_Json::decode($this->getRequest()->getRawBody());
        $response = $this->getResponse();
        $form = new Core_Form_Newsletter_List_Create();
        if ($form->isValid($data)) {
            $edited = $this->_service->edit($data);
            $response->setHttpResponseCode(200);
            $this->_helper->json($edited);
        } else {
            $response->setHttpResponseCode(400);
            $this->_helper->json($form->getMessages());
        }

    }
    
    public function deleteAction()
    {
        $listId = $this->_getParam('id');
        $deleted = $this->_service->setListId($listId)->delete();
        if ($deleted) {
            $responseCode = 204; 
        } else {
           $responseCode = 404; 
        }
        $this->getResponse()->setHttpResponseCode($responseCode);
    }

}

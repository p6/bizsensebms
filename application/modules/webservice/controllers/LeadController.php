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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Webservice_LeadController extends Zend_Rest_Controller
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Service_WebService_Rest_Lead;
    }

    /**
     * Create a lead 
     */
    public function postAction()
    {
        $data = $this->getRequest()->getPost();
        $data['assigned_to'] = $this->_model->getDefaultAssigneeId();
        $form = new Core_Form_Lead_Create();
        if ($form->isValid($data)) {
            $leadId = $this->_model->create($data); 
            $leadRecord = $this->_model->setLeadId($leadId)->fetch();
            $this->getResponse()->setHttpResponseCode(201);
            $url = 'lead/lead_id/' . $leadId;
            $this->getResponse()->setHeader('location', $url);
            $this->_helper->json($leadRecord);
            $status = true;
            $message = 'Success';
        } else {
            $status = false;
            $message = $form->getErrors();
            $this->getResponse()->setHttpResponseCode(400);
            $this->_helper->json($message);
        }
        $this->view->status = $status;
        $this->view->message = $message;

    }


    public function indexAction() {}
    public function getAction(){}
    public function  putAction(){}
    public function deleteAction(){}
}

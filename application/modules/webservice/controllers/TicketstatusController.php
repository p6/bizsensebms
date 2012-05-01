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
class Webservice_TicketStatusController extends Zend_Rest_Controller
{
    
    protected $_service;
        
    public function init()
    {
       # $this->___sayMethod();
        $this->_service = new Core_Service_WebService_Rest_Ticket_Status();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }

    protected function ___sayMethod()
    {
        $request = $this->getRequest();
        echo "action is" . $request->getActionName();
    }

    public function indexAction()
    {
        $this->_helper->json((array) $this->_service->fetchAll());
    }



    public function getAction()
    {
        $ticketStatusId = $this->_getParam('id');
        $this->_service->setTicketStatusId($ticketStatusId);
        $ticketRecord = $this->_service->fetch();
        $this->_helper->json((array) $ticketRecord);
    }
    
    public function postAction()
    {
        var_dump($_POST);
        echo "post action in ticketcomment";
    }
    
    public function putAction()
    {
       echo "put action";
    }
    
    public function deleteAction()
    {
    }
}

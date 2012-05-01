<?php
/*
 * Error handling
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
class ErrorController extends Zend_Controller_Action
{
    /**
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	    $this->_helper->layout->disableLayout();	
    }

    /**
     * @see Zend_Controller_Action
     */	
    public function errorAction()
    {
	
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                /**
                 * 404 error -- controller or action not found
                 */
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found');
                $this->view->fourNotFour = true;
                return;   
                break;
            default:
                /**
                 * application error; display error page, but don't
                 * change status code
                 */
                $exception = $errors->exception;
                $this->view->exception = $exception;
                break;
        }
    }

    /**
     * Access control violation attempt
     */	
    public function accessAction()
    {
		
    }
   
}



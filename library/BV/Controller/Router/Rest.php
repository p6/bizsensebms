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
class BV_Controller_Router_Rest
{
    /**
     * @param  object $request Zend_Controller_Request_Abstract
    */
    public function setup($request, $router)
    {
        $method = $request->getMethod();
        if ($method == 'DELETE') {
            $action = 'delete';
        } else if ($method == 'POST') {
            $action = 'post';
        } else if ($method == 'PUT') {
            $action = 'put';
        } else if ($method == 'GET') {
            $action = 'get';
        }

        $route = new Zend_Controller_Router_Route(
            'webservice/list/:list_id/subscriber/:email',
            array(
                'module' => 'webservice',
                'controller' => 'listsubscriber',
                'action' => $action,
            )
        );
        $router->addRoute('bizsense_rest_newsletter_list_subscriber', $route);

        if ($method == "PUT") {
            $action = 'put';
        } else if ($method == 'GET') {
            $action = 'index';
        }
        $route = new Zend_Controller_Router_Route(
            'webservice/list/:list_id/subscriber',
            array(
                'module' => 'webservice',
                'controller' => 'listsubscriber',
                'action' => $action
            )
        );
        $router->addRoute('bizsense_rest_newsletter_list_subscriber_index', $route);


    }
    
    /**
     * @TODO get standard REST actions
     */
    public function getAction($request)
    {
          
    }
}

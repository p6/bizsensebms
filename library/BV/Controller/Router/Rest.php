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

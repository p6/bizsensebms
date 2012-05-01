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

class BV_Controller_Plugin_Initialize extends Zend_Controller_Plugin_Abstract
{

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($frontController, array(), array('webservice'));
        $router = $frontController->getRouter();
        $router->addRoute('rest', $restRoute);
        $customRoute = new BV_Controller_Router_Rest();
        $customRoute->setup($request, $router);
    }

    /**
     * @param $request Zend_Controller_Request_abstract
     * @see Zend_Controller_Plugin_Abstract::routeShutdown
     *
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $controller = $request->getControllerName();
        $module = $request->getModuleName();
        $frontController = Zend_Controller_Front::getInstance();

        /*
         * Initialize if not in install or error controller.
         * Don't bother to check if we are in (install or error) 
         * if module is not default
         * error and install are controllers of default module only.
         */
        if ( ($module == 'default' and 
                ($controller != 'install' or $controller != 'error') ) or 
                ($module != 'default') ) {
        }
       
        /**
         * if config is not set, set controller to installer
         */
        $config = $frontController->getParam('config');
        if (empty($config)) {
            $request->setModuleName('default');
            $request->setControllerName('install');
            $request->setActionName('index');
            return;
        }
 
        $db = Zend_Registry::get('db');
        /**
         * if db is not set, set controller to error
         */
        if (empty($db)) {
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('db');
            return;
        }     
 
          
    }


    /**
     * Check the access before control is given to a controller action
     * @param $request Zend_Controller_Request_Abstract
     * @return void
     * Perform access checks 
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($module == 'default' and 
            ($controller == 'install' or $controller == 'error')) {
            return;
        }

        if ($module == 'webservice') {
            $this->_checkWebServiceAccess();
        } else {
            $this->_checkAccess();
        }

     }

    
     
    /**
     * Check the access for the page requested
     * Route to error if access check fails
     */ 
    protected function _checkAccess()
    {
        $front = Zend_Controller_Front::getInstance();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $url = $module . "/" . $controller . "/" . $action;
        
        if ($module == 'default' and ($controller == 'install' or $controller == 'error')) {
            return;
        }

        $pageAccess = new Core_Model_UrlAccess;

        $privilegeName = $pageAccess->getPrivilgeNameFromUrl($url);
        $assertion = $pageAccess->getAssertionClass($url);
        $acl = Zend_Registry::get('acl');
        $currentUser = Zend_Registry::get('user');
        $auth = Zend_Auth::getInstance();
     
        /**
         * Check access based on privilege id stored in the database
         */
        if ($privilegeName) { 
            /**
             * If user does not have access to the resource and privielge redirect
             */ 
            if (!$acl->isAllowed($currentUser, $privilegeName)){
                if ($auth->hasIdentity()) {
                    /*
                     * Redirect to error if user is authenticated
                     */
                    #$this->sendToErrorAccess();
                    $this->_alterRequest('default', 'error', 'access');
                } else {
                    /*
                     * Redirect to login page if user is anonymous
                     */
                    #$this->sendToUserLogin();
                    $this->_alterRequest('default', 'user', 'login');
                }
            }

        }   
       
        /**
         * If callback validator is available use it
         */
         if ($assertion) { 
            $assertionObject = new $assertion($request->getParams());
            /**
             * If user does not have access to the privilege redirect to access denied page
             */ 
             if (!$acl->isAllowed($currentUser, null, $assertionObject)) {
                if ($auth->hasIdentity()) {
                    /*
                     * Redirect to error if user is authenticated
                     */
                    $this->_alterRequest('default', 'error', 'access');
                 } else {
                    /**
                     * Redirect to login page if user is anonymous
                     */
                    $this->_alterRequest('default', 'user', 'login');
                 }
             }
          } 
    } 

    /**
     * Check whether the web service call is valid
     */
    protected function _checkWebServiceAccess()
    {
        $layout = Zend_Controller_Action_HelperBroker::getStaticHelper('layout');
        $layout->disableLayout(); 
        $apiKey = $this->getRequest()->getHeader('X-Bizsense-Apikey');

        $webService = new Core_Model_WebService;
        $accessAllowed = $webService->authenticate($apiKey);
        if (!$accessAllowed) {
            $this->getResponse()
                    ->setHttpResponseCode(403)
                    ->appendBody("<p>Invalid API Key</p>\n");
            $this->_alterRequest('default', 'error', 'access');         
            $webService->logInvalidResponse();
            return;
        }
       
    }

    /**
     * Alter the request object and reset the dispacthed flag 
     * @param string $module is the name of the module to be set
     * @param string $controller is the name of the controller to be set
     * @param string $action is the name of the action to be set
     */
    protected function _alterRequest($module, $controller, $action)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $request->setModuleName($module)
                ->setControllerName($controller)
                ->setActionName($action)
                ->setDispatched(false);
        
    }

}

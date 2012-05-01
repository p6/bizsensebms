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

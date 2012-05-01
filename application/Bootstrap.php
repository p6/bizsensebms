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

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

   /**
    * Initialize view
    * @return object Zend_View     
    */
    protected function _initView()
    {
        /**
         * If the application is running in CLI mode production
         * We don't need view and the front controller resources
         * This method bootstraps the resource
         * The layout resources defined in the application.ini bootstraps
         * view. Better to check environment and return before all that happens
         */
        if (PHP_SAPI == 'cli' and APPLICATION_ENV == 'production') {
            return;
        }

        $viewResource = new Zend_Application_Resource_View();
        $view = $viewResource->init();
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv(
            'Content-Type', 'text/html;charset=utf-8'
        );
        $view->setHelperPath('BV/View/Helper', 'BV_View_Helper');
        $view->addScriptPath(APPLICATION_PATH . '/../library/BV/View/Partial');

        /**
         * Setup dojo environment
         */
        Zend_Dojo::enableView($view);
        $view->dojo()->setLocalPath('/js/dojo/dojo/dojo.js');        
       
        $view->messages = Zend_Controller_Action_HelperBroker::getStaticHelper(
                            'FlashMessenger')->getMessages();

        return $view;
    }

    /**
     * Initizlize the request
     * @return object Zend_Controller_Request_Abstract
     */
    protected function _initRequest()
    {
        if (PHP_SAPI == 'cli') {
            return;
        }
        $this->bootstrap('session');
        /** 
         * Ensure the front controller is initialized
         */
        $this->bootstrap('FrontController');

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');
        $this->bootstrap('config');
        $config = $this->getResource('config');
        $front->setParam('config', $config); 

        $request = new Zend_Controller_Request_Http();
        $request->setBaseUrl('/');
        $front->setRequest($request);

        /** 
         * Ensure the request is stored in the bootstrap registry
         */
        return $request;
    }

    /**
     * Initialize the autoloader
     * @return object Zend_Application_Module_Autoloader
     */
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Core_',
            'basePath'  => dirname(__FILE__) . '/modules/default'
        ));
        /**
         * Right place to set the plugin loader cache
         * Before all other ZF components are used the plugin loader
         * Cache needs to be set for maximum benefit
         * Therefore this is not a regular bootstrap resource
         */
        $this->_nonResourceInitIncludeFileCache();
        return $autoloader;
    }

    /**
     * Sets the include file cache
     */
    protected function _nonResourceInitIncludeFileCache()
    {
        if (APPLICATION_ENV != 'production') {
            return;
        }

        $classFileIncCache = APPLICATION_PATH . '/data/cache/pluginLoaderCache.php';      
        if (file_exists($classFileIncCache)) {
            include_once $classFileIncCache;
        }         
        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);

    }


    /**
     * Initialize the session
     * @return void
     */
    protected function _initSession()
    {
        if (PHP_SAPI == 'cli') {
            require_once 'Zend/Session.php';
            Zend_Session::$_unitTestEnabled = true;
            return;
        }
        Zend_Session::start();
        return null;
    }

    /**
     * Initialize the config object
     * @return Zend_Config object containing DB configuration
     */
    protected function _initConfig()
    {
        $config = null;
        if (is_readable(APPLICATION_PATH . '/configs/database.ini')) {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini');
            Zend_Registry::set('config', $config);
        }
        return $config;
    }

    /**
     * Initialize the database
     * @return object Zend_Db_Adapter_Abstract
     */    
    protected function _initDatabase()
    {
        $db = null; 
        $this->bootstrap('config');
        $config = $this->getResource('config');
        if (!empty($config)) {
            $db = Zend_Db::factory($config->database);
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            Zend_Registry::set('db', $db);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
        }

        if (APPLICATION_ENV == 'development' and $db) {
            $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            $profiler->setEnabled(true);
            $db->setProfiler($profiler);
            $this->bootstrap('request'); 
            $response = new Zend_Controller_Response_Http();
            $channel  = Zend_Wildfire_Channel_HttpHeaders::getInstance();
            $this->bootstrap('request');
            $request = $this->getResource('request');
            $channel->setRequest($request);
            $channel->setResponse($response);

            // Start output buffering
            ob_start();
            
        }
        return $db;
    }

   /**
    * Initialize the timezone
    * @return object Zend_Locale
    */
    protected function _initTimezone()
    {
        $this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }
        $timezone = $db->fetchOne('SELECT timezone FROM settings');
        $date = new Zend_Date();
        if($timezone) {
            $date->setTimezone($timezone);
            date_default_timezone_set($timezone);
        }
        else {
            date_default_timezone_set('UTC');
            $date->setTimezone('UTC');
        }
    } 

    /**
     * Initialize the cache
     * return object Zend_Cache 
     */
    protected function _initCache()
    {
        $this->bootstrap('config');
        $config = $this->getResource('config');
        if (empty($config)) {
            return;
        }

        $frontendOptions = array(
           'automatic_serialization' => true
        );
  
        $frontendOptions['cache_id_prefix'] = $config->application->cache_id;           
        $backendOptions  = array(
            'cache_dir' => APPLICATION_PATH . '/data/cache'
        );

       $backendOptions['server'] = array(array('host' => 'localhost', 'port' => 11211,
                        'persistent' => true, 'weight' => 1,
                        'retry_interval' => 15, 'status' => true,
                        'failure_callback' => '' ));

        $cacheBackend = 'Memcached';

        $cache = Zend_Cache::factory(
            'Core',
            $cacheBackend,
            $frontendOptions,
            $backendOptions
        );
        
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Registry::set('cache', $cache);

        return $cache; 
    }

    /**
     * Initialize the user resource
     * @return Core_Model_User
     */
    protected function _initUser()
    {
        $this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }

        $auth =  Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $db = Zend_Registry::get('db');
            $validator = new Zend_Validate_EmailAddress();
            $identity = $auth->getIdentity();
            if ($validator->isValid($identity)) {
                $uid = $db->fetchOne('SELECT user_id FROM user WHERE email = ?', $identity);
            } else {
               $uid = $db->fetchOne('SELECT user_id FROM user WHERE username = ?', $identity);
            }
            $user = new Core_Model_User($uid);
        } else {
            $user = new Core_Model_User_Anonymous();
        }
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->currentUser = $user;
        Zend_Registry::set('user', $user);
        return $user;

    }

    /**
     * Initialize the ACL resource
     * @return Core_Model_Acl
     */
    protected function _initAcl()
    {
        $this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }
        $acl = new Core_Model_Acl;
        $acl->init();
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->acl = $acl;
        Zend_Registry::set('acl', $acl);
        return $acl;
    }
 


}



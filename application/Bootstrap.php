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
        }
        else {
            $date->setTimezone('Asia/Calcutta');
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



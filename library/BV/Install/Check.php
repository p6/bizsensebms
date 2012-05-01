<?php
/*
 * Check if application is installed
 *
 *
 * LICENSE: GNU GPL V3
 *
 * This source file is subject to the GNU GPL V3 license that is bundled
 * with this package in the file license
 * It is also available through the world-wide-web at this URL:
 * http://bizsense.binaryvibes.co.in/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryvibes.co.in so we can send you a copy immediately.
 *
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @license    http://bizsense.binaryvibes.co.in/license   
 * @version    $Id:$
 */


class BV_Install_Check extends Zend_Controller_Plugin_Abstract
{
    public $db;

    public function __construct()
    {
     //   $this->db = Zend_Registry::get('db');
    }


	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {

		$module = $request->getModuleName();
	    $controller = $request->getControllerName();
        $action = $request->getActionName();
	    $url = $module . "/" . $controller . "/" . $action;
        
        $plugin = 'BV_Controller_Plugin_Acl';
        if ($controller == 'install') {
               Zend_Controller_Front::getInstance()->unregisterPlugin($plugin);
        }    
       
        if ($controller != 'install') { 
        try {
            $config = new Zend_Config_Ini('../application/config/config.ini');
            $readData = $config->application->install->get('status');

        } catch (Zend_Config_Exception $e) {
            /* 
             * there is no config file or it is not readable
             */
            $request->setModuleName('default');
            $request->setControllerName('install');
            $request->setActionName('index');
            
            return;
        }

		try {
		    $config = new Zend_Config_Ini('../application/config/config.ini');
            $db = Zend_Db::factory($config->database);
            $db->getConnection();

            $sample = $db->fetchOne("SELECT uid FROM user WHERE uid=1");   

		} catch (Zend_Db_Statement_Exception $e) {
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('db');
            echo  "<h1>Ann error occured</h1> There appears to be a problem with the database. Please contact
                the administrator";
            return;
		} catch (Zend_Db_Adapter_Exception $e) {
            /*
             * perhaps a failed login credential, or perhaps the RDBMS is not running
             */
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('db');
            echo  "<h1>An error occured</h1> Connection to database could not be established. Please contact
            the webmaster.";
            exit(); 
            return;
        } catch (Zend_Config_Exception $e) {
            /* 
             * there is no config file or it is not readable
             */
            $request->setModuleName('default');
            $request->setControllerName('install');
            $request->setActionName('index');
            
            return;
        }

        /*    $sql = "SELECT url FROM urlAccess WHERE url = '$url'";
            $urlExists = $db->fetchOne($sql);
		if (!$urlExists) {
           $request->setModuleName('default');
           $request->setControllerName('error');
           $request->setActionName('fournotfound');
            return;
		}

        */


        }




	}
}

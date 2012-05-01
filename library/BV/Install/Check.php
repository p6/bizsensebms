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

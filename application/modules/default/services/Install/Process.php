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
 * You can contact Binary Vibes Information Technologies Pvt. 
 * Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
class Core_Service_Install_Process
{
    /*
     * Accumulates the error messages in the install process
     */
    protected $_messages = array();

    /*
     * The user input from the install form
     */
    protected $_input = array();

    public function checkFilePermissions()
    {
        
        $toReturn = true;

        $pathsToCheck = array(
            APPLICATION_PATH . '/configs',
            APPLICATION_PATH . '/data',
            APPLICATION_PATH . '/data/logo',
            APPLICATION_PATH . '/data/log',
            PUBLIC_PATH . '/files/logo',
        );

        foreach ($pathsToCheck as $path) {
            if (!is_writeable($path)) {
                $this->_messages[] = "Make sure $path is writeable";
                $toReturn = false;
            }
        }

        return $toReturn;

    }

    /*
     * Determine whether the application is already installed
     * If yes, redirect to acess denied page
     */    
    public function checkAccess()
    {
        if (is_readable(APPLICATION_PATH . '/configs/database.ini')) {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/application/configs/database.ini');
            if (isset($config->application->install) and $config->application->install->get('status') == "1") {
                return false;
            }
        }
        return true;
    }    
    
    /*
     * Store the input from install form
     */
    public function setInput($input = array())
    {
        $this->_input = $input;
    }
      
    public function writeConfig()
    {
        $dbName = $this->_input['db_name'];
        $dbUsername = $this->_input['db_username'];
        $dbPassword = $this->_input['db_password'];
        $adminEmail = $this->_input['admin_email'];


        $pdoParams = array(
           PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
        );
        $parameters = array(
            'host'     => $this->_input['db_hostname'],
            'username' => $dbUsername,
            'password' => $dbPassword,
            'dbname'   => $dbName,
            'driver_options' => $pdoParams
       );
      
        $config = new Zend_Config(array(), true);
        $config->database = array();
        $config->database->adapter = "pdo_mysql";
        $config->database->params = array();
        $config->database->params->host = $this->_input['db_hostname'];
        $config->database->params->username = $dbUsername;
        $config->database->params->password = $dbPassword;
        $config->database->params->dbname = $dbName;
        $config->database->params->charset = 'utf8';
        $config->database->params->driver_options_1002 = 'SET NAMES utf8';
        $config->application = array();
        $config->application->install = array();
        $config->application->install->status = 1;
        $config->application->cache_id = $this->getRandomCacheId();
        $writer = new Zend_Config_Writer_Ini(array
            (
                'config'   => $config,
                'filename' => APPLICATION_PATH . '/configs/database.ini'
            )
        );
        $writer->setRenderWithoutSections();
        $writer->write();

    } 

    public function getRandomCacheId()
    {
        $container = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9));
        $output = '';
        for ($i=0; $i < 10; $i++) {
            $output .= $container[mt_rand(0,count($container)-1)];
        }
        return $output;  
    }

    /*
     * @Return error messages accumulated in the install process
     */ 
    public function getMessages()
    {
        return $this->_messages;            
    }
    

    /* Create tables
     *
     */
    public function createTables()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini');
        $db = Zend_Db::factory($config->database);

        $installSql = file_get_contents(APPLICATION_PATH . '/modules/default/services/Install/Sql/MySQL.sql', 'r');
        $array = explode(";", $installSql);

        foreach ($array as $table) {
            if (strlen($table) > 3) {
                $db->getConnection()->exec($table);
            }
        }

        $db->closeConnection();
        unset($db);
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini');
        $db = Zend_Db::factory($config->database);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $db->getConnection();
        Zend_Registry::set('db', $db);
 
    }

    /*
     * Fill db tables
     */
    public function fillTables()
    {
      $tableFill = new Core_Service_Install_TableFill_BizSense($this->_input);  
    }
}


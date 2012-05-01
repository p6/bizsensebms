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
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini');
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


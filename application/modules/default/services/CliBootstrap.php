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
class Core_Service_CliBootstrap 
{
    protected $_application;

    public function init()
    {
        defined('APPLICATION_PATH')
            || define('APPLICATION_PATH',
              realpath(dirname(__FILE__) . '/../../../../application/'));

        defined('APPLICATION_ENV')
            || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : 'production'));

        set_include_path(implode(PATH_SEPARATOR, array(
             APPLICATION_PATH . '/../library',
            get_include_path(),
        )));

        require_once 'Zend/Application.php';

        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $application->bootstrap(array('autoload', 'config', 'database', 'cache'));
        $this->_application = $application;
    }
 
    public function run()
    {
        set_time_limit(0);
        $options = new Zend_Console_Getopt(
            array(
                'service|s=s'    => 'service to run example --service=Core_Service_Cron',
                'reinstall=s'     => 'file. Ex: reinstall=true',
                'process-bounce|p=s' => 'TRUE|FALSE. Should we process bounce messages?'
            )
        );

        try {
            $options->parse();
            $serviceName = $options->getOption('s');
            if (class_exists($serviceName)) {
                $service = new $serviceName;
                $service->run();
                $service->setConsoleOpt($options);
            } else {
                echo PHP_EOL . "No matching service found" . PHP_EOL;
            }
        } catch (Zend_Console_Getopt_Exception $e) {
            echo $e->getUsageMessage();
            exit;
        }
    }
 

}

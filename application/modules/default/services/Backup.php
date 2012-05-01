<?php
/*
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

class Core_Service_Backup extends Core_Service_CliAbstract 
    implements Core_Service_CliInterface
{


    public function run()
    {
        $this->backupDatabase();
        
    }

    public function backupDatabase()
    {
        $date = new Zend_Date();
        $fileName = $date->get(Zend_Date::YEAR) . 
                    '-' . 
                    $date->get(Zend_Date::MONTH) . 
                    '-' . 
                    $date->get(Zend_Date::DAY) .
                    '-' . 
                    $date->get(Zend_Date::HOUR) .
                    '-' . 
                    $date->get(Zend_Date::MINUTE) .
                    '-' . 
                    $date->get(Zend_Date::SECOND)
                    ;
        $config = Zend_Registry::get('config');
        $dbUsername = $config->database->params->username;
        $dbPassword = $config->database->params->password;
        $dbName = $config->database->params->dbname;

        $file = APPLICATION_PATH . '/data/backup/' . $fileName . '.sql';
        $command = sprintf(
            "mysqldump -u %s --password=%s -d %s --skip-no-data > %s", 
            escapeshellcmd($dbUsername), 
            escapeshellcmd($dbPassword), 
            escapeshellcmd($dbName), 
            escapeshellcmd($file)
        );
        exec($command);
    }

}

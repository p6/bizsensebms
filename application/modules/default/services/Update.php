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
class Core_Service_Update extends Core_Service_CliAbstract 
    implements Core_Service_CliInterface
{

    protected $_previousAccessMap = array();

    public $db;

    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    public function run()
    {
        $this->message("Running BizSense update...");

        $this->message("Backuping up database...");
        $backupService = new Core_Service_Backup;
        $backupService->backupDatabase();
        $this->message("Done");

        $this->message("Retrieving current ACL...");
        $this->retrieveCurrentAcl();
        $this->message("Done");

        $this->message("Updating database schema...");
        $this->updateSchema();
        $this->message("Done");

        $this->message("Restoring ACL settings...");
        $this->restoreOldAcl();
        $this->message("Done");

        $this->message("Updating url access...");
        $this->updateUrlAccess();
        $this->message("Done");

        $this->message("Updating version...");
        $this->updateVersion();
        $this->message("Done");

        $this->message("Adding default data for new features...");
        $this->fillApplicationData();
        $this->message("Done");

        $this->message("Finished all tasks");
        $this->message(PHP_EOL);
        
    }

    public function updateSchema()
    {
        $updateSql = file_get_contents(APPLICATION_PATH 
            . '/modules/default/services/Update/Update.sql', 'r');
        $array = explode(";", $updateSql);

        foreach ($array as $query) {
            if (strlen($query) > 3) {
                $this->db->getConnection()->exec($query);
            }
        }
    }

    public function retrieveCurrentAcl()
    {
        $accessModel = new Core_Model_Access;
        $access = $accessModel->fetchAll();
        $privilegeModel = new Core_Model_Privilege;
        foreach ($access as $accessItem) {    
            $privilegeModel->setPrivilegeId($accessItem['privilege_id']);
            $privilege = $privilegeModel->fetch();
            $this->_previousAccessMap[] = array(
                'role_id' => $accessItem['role_id'],
                'privilege_name'=> $privilege['name']
            );
        }
        
    }

    public function loadPrivileges()
    {
        $this->db->query("delete from access");
        $this->db->query("ALTER TABLE access AUTO_INCREMENT = 1");

        $this->db->query("delete from privilege");
        $resourceModel = new Core_Model_Resource;
        $privilegeModel = new Core_Model_Privilege;       

        $privilegeFile = file_get_contents(APPLICATION_PATH 
            . '/modules/default/services/Install/TableFill/Privileges.json');
        $privileges = json_decode($privilegeFile);
       
        foreach ($privileges as $privilege) {
            $privilegeModel->create(array('name'=> $privilege));
        }

    }

    public function restoreOldAcl()
    {
        $this->loadPrivileges();
        $privilegeModel = new Core_Model_Privilege;
        $dataToInsert = array();
        foreach ($this->_previousAccessMap as $acl) {
            $dataToInsert['role_id'] = $acl['role_id'];    
            $dataToInsert['privilege_id'] = 
                $privilegeModel->getIdByName($acl['privilege_name']);
            if (is_numeric($dataToInsert['privilege_id'])) {
                $this->db->insert('access', $dataToInsert);
            }
        }
            
    }

    /**
     * Fill the url_access table
     */
    public function updateUrlAccess()
    {
        $this->db->query("delete from url_access");
        $this->db->query("ALTER TABLE url_access AUTO_INCREMENT = 1");

        $urlAccessModel = new Core_Model_UrlAccess;
        $urlAccessFile = file_get_contents(APPLICATION_PATH 
            . '/modules/default/services/Install/TableFill/UrlAccess.json');
        $urlAccessContent = json_decode($urlAccessFile);
        if (!is_array($urlAccessContent)) {
            return;
        }
        if (!count($urlAccessContent)) {
            return;
        }

        foreach ($urlAccessContent as $record) {
            $urlAccessModel->insertByPrivilegeName((array) $record);
        }

    }

    /**
     * Update the BizSense version
     */
    public function updateVersion()
    {
       $variable = new Core_Model_Variable;
       $variable->save('version', '0.3.1-alpha');
    }

    /**
     * Fill default data for version 0.2
     */
    public function fillApplicationData()
    {        
        
    }
}

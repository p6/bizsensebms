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

Interface
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

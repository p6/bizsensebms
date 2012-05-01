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
class Core_Service_Install_DemoData extends Core_Service_CliAbstract 
    implements Core_Service_CliInterface
{
    public $db;

    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    public function run()
    {
       $this->message("Inserting Contacts...");
       $this->insertContacts(); 
       $this->message("Done");
       
       $this->message("Inserting Leads...");
       $this->insertLeads(); 
       $this->message("Done");
       
       $this->message("Inserting Accounts...");
       $this->insertAccounts(); 
       $this->message("Done");
       
       $this->message("Inserting Oppurtunities...");
       $this->insertOppurtunities();
       $this->message("Done");
       
       $this->message("Inserting Newsletter Message...");
       $this->insertNewsletterMessage(); 
       $this->message("Done");
       
       $this->message("Inserting Vendor...");
       $this->insertVendor();
       $this->message("Done");
       
       $this->message("Inserting Message Queue...");
       $this->insertQueue(); 
       $this->message("Done");
    }
    
    public function insertRoles()
    {
        $this->executeQueriesFromFile(APPLICATION_PATH . 
                    '/modules/default/services/Install/Sql/demodata/role.sql');        
    }

    public function insertLeads()
    {
        $this->executeQueriesFromFile(APPLICATION_PATH . 
                    '/modules/default/services/Install/Sql/demodata/lead.sql');        
    }

    public function insertContacts()
    {
        $this->executeQueriesFromFile(APPLICATION_PATH . 
                  '/modules/default/services/Install/Sql/demodata/contact.sql');        
    }

    public function insertAccounts()
    {
        $this->executeQueriesFromFile(APPLICATION_PATH . 
                  '/modules/default/services/Install/Sql/demodata/account.sql');        
    }
    
    public function insertOppurtunities()
    {
        $this->executeQueriesFromFile(APPLICATION_PATH . 
              '/modules/default/services/Install/Sql/demodata/oppurtunity.sql');        
    }
    
    public function insertVendor()
    {
        $this->executeQueriesFromFile(APPLICATION_PATH . 
              '/modules/default/services/Install/Sql/demodata/vendor.sql');        
    }


    public function executeQueriesFromFile($filePath)
    {
        $db = $this->db;
        $installSql = file_get_contents($filePath, 'r');
        $array = explode(";", $installSql);

        foreach ($array as $table) {
            if (strlen($table) > 3) {
                $db->getConnection()->exec($table);
            }
        }

    }

    public function insertQueue()
    {
        $db = $this->db;
        
        for ($i = 1; $i < 10; $i++) {
            $sample_list = "sample list - ".$i;
            $toInsert = array(
                'name' => $sample_list,
                'description' => 'test list',
                'created' => time(),
                'created_by' => 1,
            );
            $db->insert('list', $toInsert);
        }    
        
        $domains = array();
        for ($i = 1; $i < 101; $i++) {
            $domains[] = 'example' . $i . '.com';
        }

        for ($i = 1;  $i < 300; $i++) {
            $random = rand(1, 99);
            $domain = $domains[$random];
            $email = 'user' . $i . '@' . $domain;
            $toInsert = array(
                'first_name' => 'Joe',
                'middle_name' => 'm',
                'last_name' => 'Doe',
                'email' => $email,
                'domain' => $domain,
                'format' => 1,
                'status' => 3,
           );
           $db->insert('subscriber', $toInsert);
        }
        
                    
        for ($j = 1;  $j < 300; $j++) {
            $toInsert = array('list_id' => '1','subscriber_id' => $j);
            $db->insert('list_subscriber', $toInsert);
        }
       
        
        for ($i = 1;  $i < 100; $i++) {
            $toInsert = array(
                'status' => '0',
                'message_id' => $i,
                'list_id' => 1,
                'subscriber_id' => $i,
           );
           $db->insert('message_queue', $toInsert);
        }
        
        
    }

    public function insertNewsletterMessage()
    {
        $start = time();
        $db = $this->db;
        $sample_html = "<b>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
          Mauris ut orci non sem consequat mollis. Vivamus eget diam nec arcu 
          adipiscing adipiscing. Integer nisl nisi, vestibulum vel vehicula 
          dapibus, dictum quis dui. Etiam hendrerit blandit augue vitae 
          vulputate. Quisque lobortis scelerisque ullamcorper. Aliquam pulvinar 
          luctus adipiscing. Aenean adipiscing lorem eget mauris venenatis 
          varius. In eget justo ut velit euismod laoreet quis non tortor. 
          Donec faucibus cursus ipsum eget malesuada. In vestibulum scelerisque 
          magna id tincidunt. Nam blandit, mi eu egestas lobortis, mauris felis 
          auctor justo, id suscipit metus dui et elit. Sed eu erat sed est 
          ornare pulvinar. Cum sociis natoque penatibus et magnis dis parturient 
          montes, nascetur ridiculus mus. Nunc consequat, velit sit amet 
          tincidunt viverra, mi ligula bibendum libero, vitae congue elit orci 
          interdum neque. Integer varius justo lacus. Curabitur rutrum porta 
          fermentum. Nulla facilisi. Nulla facilisi. Etiam facilisis, tortor 
          in viverra congue, ipsum ipsum ultricies lectus, sed tristique nulla 
          ante et ante.</b> ";
        
        $sample_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
          Mauris ut orci non sem consequat mollis. Vivamus eget diam nec arcu 
          adipiscing adipiscing. Integer nisl nisi, vestibulum vel vehicula 
          dapibus, dictum quis dui. Etiam hendrerit blandit augue vitae 
          vulputate. Quisque lobortis scelerisque ullamcorper. Aliquam pulvinar 
          luctus adipiscing. Aenean adipiscing lorem eget mauris venenatis 
          varius. In eget justo ut velit euismod laoreet quis non tortor. 
          Donec faucibus cursus ipsum eget malesuada. In vestibulum scelerisque 
          magna id tincidunt. Nam blandit, mi eu egestas lobortis, mauris felis 
          auctor justo, id suscipit metus dui et elit. Sed eu erat sed est 
          ornare pulvinar. Cum sociis natoque penatibus et magnis dis parturient 
          montes, nascetur ridiculus mus. Nunc consequat, velit sit amet 
          tincidunt viverra, mi ligula bibendum libero, vitae congue elit orci 
          interdum neque. Integer varius justo lacus. Curabitur rutrum porta 
          fermentum. Nulla facilisi. Nulla facilisi. Etiam facilisis, tortor 
          in viverra congue, ipsum ipsum ultricies lectus, sed tristique nulla 
          ante et ante. ";
        for ($i = 1; $i < 100; $i++) {
            $toInsert = array(
                'subject' => 'Sample subject',
                'text' => $sample_text,
                'html' => $sample_html,
                'created_by' => 1,
                'created' => time(),
            );
            $db->insert('message', $toInsert);
        }             
    }
}    
    
     

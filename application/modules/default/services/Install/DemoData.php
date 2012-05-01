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
    
     

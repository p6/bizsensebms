#!/usr/bin/php
<?php
chdir(dirname(realpath(__FILE__)));

// // // // // // // // // require_once '../CliInit.php';

$demoInstaller = new InsertDemoData($db, $scriptsPath);
$demoInstaller->insertAll();

class InsertDemoData 
{
    /**
     * The database adapter
     */
    protected $_db;

    /**
     * Path where the scripts are located
     */
    protected $_scriptsPath;

    public function __construct($db, $scriptsPath)
    {
        $this->_db = $db;
        $this->_scriptsPath = $scriptsPath;
    }

    /** 
     * Install all demo data
     */
    public function insertAll()
    {
#       $this->insertRoles(); 
       $this->insertContacts(); 
       $this->insertLeads(); 
       $this->insertAccounts(); 
       $this->insertOppurtunities();
       $this->insertNewsletterMessage(); 
       $this->insertVendor();
       $this->insertQueue(); 
       //$this->insertQueueType2(); 
    }

    /**
     * Insert user roles
     */
    public function insertRoles()
    {
        $this->executeQueriesFromFile($this->_scriptsPath . '/Install/Demo/Data/Sql/role.sql');        
    }

    public function insertLeads()
    {
        $this->executeQueriesFromFile($this->_scriptsPath . '/Install/Demo/Data/Sql/lead.sql');        
    }

    public function insertContacts()
    {
        $this->executeQueriesFromFile($this->_scriptsPath . '/Install/Demo/Data/Sql/contact.sql');        
    }

    public function insertAccounts()
    {
        $this->executeQueriesFromFile($this->_scriptsPath . '/Install/Demo/Data/Sql/account.sql');        
    }
    
    public function insertOppurtunities()
    {
        $this->executeQueriesFromFile($this->_scriptsPath . '/Install/Demo/Data/Sql/oppurtunity.sql');        
    }
    
    public function insertVendor()
    {
        $this->executeQueriesFromFile($this->_scriptsPath . '/Install/Demo/Data/Sql/vendor.sql');        
    }


    public function executeQueriesFromFile($filePath)
    {
        $db = $this->_db;
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
        $db = $this->_db;
        $domains = array();
        for ($i = 1; $i < 101; $i++) {
            $domains[] = 'example' . $i . '.com';
        }

        for ($i = 1;  $i < 3000; $i++) {
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
        
        $end = time();
        $difference = $end - $start;
        echo PHP_EOL . "It took " . $difference . " seconds to insert dummy queue data" . PHP_EOL;

    }

    public function insertNewsletterMessage()
    {
        $start = time();
        $db = $this->_db;
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
        
        for ($i = 1; $i < 100; $i++) {
            $sample_list = "sample list - ".$i;
            $toInsert = array(
                'name' => $sample_list,
                'description' => 'test list',
                'created' => time(),
                'created_by' => 1,
            );
            $db->insert('list', $toInsert);
        }     


    }
     
    public function insertQueueType2()
    {
        /*$db = $this->_db;
        $domain = 'example.com';
        for ($i = 1; $i <= 3000; $i++) {
            $email = 'user' . $i . '@example.com';
            $toInsert = array(
                'first_name' => 'Jon',
                'middle_name' => 'M',
                'last_name' => 'Doe',
                'email' => $email,
                'domain' => $domain,
                'format' => 1,
                'message_id' => 1,
                'list_id' => 1,
            );
            $db->insert('message_queue', $toInsert);
        }
        echo PHP_EOL . "Inserted " . ($i-1) . " records to message queue" . PHP_EOL;*/
    }
}




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

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

/**
 * Every web services client to BizSenese has to be registered
 * An application API key is required to access the BizSense web services
 */
class Core_Model_WebService extends Core_Model_Abstract
{

    /**
     * @var the application object
     */
    protected $_application;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_WebService';


 
    /**
     * When the web service request is forbidden log the client and access 
     * information
     */
    public function logInvalidResponse()
    {
                                                                    
        $logger = new Core_Service_Log;           
        $logger->info(sprintf("invalid webservice request no api key from %s",$_SERVER['REMOTE_ADDR']));

    }

    /**
     * @return object Core_Model_WebService_Application
     */
    public function getApplication()
    {
        if ($this->_application === null) {
            $this->_application = new Core_Model_WebService_Application;
        }
        return $this->_application;
    }

    /**
     * Check if the web service request sent a valid API key
     */
    public function authenticate($apiKey)
    {
        $access = $this->getApplication()->isAllowed($apiKey);
        return $access;
    }

    /**
     * Save the self service client application URL
     * @param array $data the URL of the client application 
     * and the id of the ss_
     */
    public function saveSelfServiceUrl($data)
    {
        unset($data['submit']);
        $table = new Core_Model_DbTable_WebService_SelfServiceClient;                                                                                                                       $current = $this->getSelfServiceUrl();            
        if ($current) {
           $table->update($data); 
        } else {
           $table->insert($data);
        }
    }

    /**
     * @return string URL of the self service portal
     */
    public function getSelfServiceUrl()
    {
        $table = new Core_Model_DbTable_WebService_SelfServiceClient;                                                                                                                       $select = $table->select();
        $result = $table->fetchRow($select);
        if ($result) {
            $rowArray = $result->toArray(); 
            return $rowArray['url'];
        } else {
            return false;
        }
    }


}

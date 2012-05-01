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
 * @category BizSense
 * @package  Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

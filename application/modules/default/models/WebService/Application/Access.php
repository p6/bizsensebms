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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */


/**
 * Every web services client to BizSenese has to be registered
 * An application API key is required to access the BizSense web services
 */
class Core_Model_WebService_Application_Access extends Core_Model_Abstract
{

    /**
     * When the application attempts to access the web service
     * One of the following has to happen 
     */
    const APPPLICATION_ACCESS = 'application access';
    const APPLICATION_ACCESS_SUCCESS = '1';
    const APPLICATION_ACCESS_FAIL = '2';
    const APPLICATION_ACCESS_DENY = '3';

    /**
     * @var the application id
     */
    protected $_application_access_id;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_WebService_Application_Access';
 

    /**
     * Create the application
     * @param array $data to be stored
     * @return the id of the application created
     */
    public function create($data = array())
    {
    }


}


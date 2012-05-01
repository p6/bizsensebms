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
class Core_Model_WebService_Application extends Core_Model_Abstract
{

    /**
     * The application status
     */
    const STATUS = 'application status';
    const STATUS_BLOCKED = '0';
    const STATUS_ACTIVE = '1';


    /**
     * @var the application id
     */
    protected $_application_id;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_WebService_Application';
 
    /**
     * @var int the web service applicationd ID
     */
    protected $_wsApplicationId;

    /**
     * @return int the web service application ID
     */
    public function getWsApplicationId()
    {
        return $this->_wsApplicationId;
    }

    /**
     * @param int the web service application ID
     * @return fluent interface
     */
    public function setWsApplicationId($wsApplicationId)
    {
        $this->_wsApplicationId = $wsApplicationId;
        return $this;
    }

    /**
     * Create the application
     * @param array $data to be stored
     * @return the id of the application created
     */
    public function create($data = array())
    {
        /**
         * Add the log columns  
         */
        $data['created'] =  time();
        $data['created_by'] = Core_Model_User_Current::getId();


        /**
         * Generate the API key for the application
         */
        $data['api_key'] = sha1($data['name'] . time()); 

         
        $dataToInsert = $this->unsetNonTableFields($data);

        $this->_application_id = $this->getTable()->insert($dataToInsert);
        return $this->_application_id;
    }


    /**
     * Determine whether the application is allowed to accesss
     *
     * @param string $apiKey
     * @return bool
     */
    public function isAllowed($apiKey)
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where("api_key = ?", $apiKey)
            ->where("status = 1");
        $result = $table->fetchRow($select);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto(
                        'ws_application_id = ?', $this->_wsApplicationId);
        $result = $table->delete($where);
        return $result;
    }
}


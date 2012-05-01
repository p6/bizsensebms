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


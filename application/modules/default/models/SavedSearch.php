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

class Core_Model_SavedSearch extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const TYPE_LEAD = '1';
    const TYPE_OPPORTUNITY = '2';
    const TYPE_ACCOUNT = '3';
    const TYPE_CONTACT = '4';

    /**
     * @var list model
     */
    protected $_model;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_SavedSearch';

    /**
     * @var int saved search Id
     */
    protected $_savedSearchId;
    
    /**
     * @param int $savedSearchId
     * @return fluent interface
     */
    public function setSavedSearchId($savedSearchId)
    {
        $this->_savedSearchId = $savedSearchId;
        return $this;
    }

    /**
     * @return int saved search ID
     */
    public function getSavedSearchId()
    {
        return $this->_savedSearchId;
    }

    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('saved_search_id = ?', $this->_savedSearchId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }


    /*
     * Inserts a record in the saved_search table
     * @param $saveId form input
     */
    public function create($name, $data = array(), $type)
    {
        $table = $this->getTable();
        $search['name'] = $name;
        $search['type'] = $type;
        
        $serializedData = serialize($data);
        $data['created'] = time();
        $data['created_by'] = $this->getCurrentUser()->getUserId();

        $search['s_criteria'] = $serializedData;
        $search['created'] = $data['created'];
        $search['created_by'] = $data['created_by'];

        $saveSearchId = $table->insert($search);
        $this->_savedSearchId = $saveSearchId;
        return $saveSearchId;
    }
    
    /**
     * @param int $type
     * @param int $userId
     * @return array rows where type user condition 
     *   match from saved_search record
     */
    public function fetchAll($type, $user)
    {
        $userId = $user->getUserId();
        $table = $this->getTable();
        $select = $table->select();
        $select->where('type = ?', $type)
               ->where('created_by = ?', $userId);
        $result = $table->fetchAll($select);
        if ($result) {
            return $result->toArray();
        }
        return $result; 
    }

    /**
     * @return int the number or records deleted
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('saved_search_id = ?', $this->_savedSearchId);
        $result = $table->delete($where);
        return $result;
    }

    /**
     * @param int type the saved search type
     * @return string name
     */
    public function getNameByType($type)
    {
        if ($type == self::TYPE_LEAD) {
            return 'lead';
        } else if ($type == self::TYPE_OPPORTUNITY) {
            return 'opportunity';
        }  else if ($type == self::TYPE_CONTACT) {
            return 'contact';
        }  else if ($type == self::TYPE_ACCOUNT) {
            return 'account';
        }
    }

    public function getType()
    {
        $row = $this->fetch();
        return $row['type'];
                
    }
}

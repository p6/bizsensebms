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

class Core_Model_Newsletter_DomainBlacklist extends Core_Model_Abstract
{
    /**
     * The domain id
     */
    protected $_domainBlacklistId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Newsletter_DomainBlacklist';

    /*
     * @param int $listId 
     * @return fluent interface    
     */
    public function setDomainBlacklistId($domainBlacklistId)
    {
        if (is_numeric($domainBlacklistId)) {
            $this->_domainBlacklistId = $domainBlacklistId;
        }
        return $this;
    }

    /**
     * return listId
     */
   public function getDomainBlacklistId()
   {
        return $this->_domainBlacklistId;
   }
    /**
     * @return array the domains blacklist record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('domain_blacklist_id = ?', 
                                                $this->_domainBlacklistId);
        $result = $table->fetchRow($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
 
    /*
     * Inserts a record in the list table
     * @param $campaignData form input
     */
    public function create($domainBlacklistData = array())
    {
        $table = $this->getTable();
        $this->_domainBlacklistId = $table->insert($domainBlacklistData);
        return $this->_domainBlacklistId;
    }

    /**
     * Updates the row in the Campaign table
     * @param array $data
     * @return int campaign ID
     */ 
    public function edit($data = array()) 
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('domain_blacklist_id = ?', 
                                                    $this->_domainBlacklistId);
        $result = $table->update($data, $where);
        return $result;
    }    

    /** 
     * Deletes a row in the campaign table
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('domain_blacklist_id = ?', 
                                                    $this->_domainBlacklistId);
        $result = $table->delete($where);
        return $result;
    }
    
}

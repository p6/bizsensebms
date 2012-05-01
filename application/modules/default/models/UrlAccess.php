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
class Core_Model_UrlAccess extends Core_Model_Abstract 
{
    /**
     * Database table Zend_Db_Table object
     */
    protected $_table;

    /**
     * Table class name
     */
    protected $_dbTableClass = 'Core_Model_DbTable_UrlAccess';

    /** 
     * @param array $record
     * Keys - page - URL to be protected no leading and trailing slashes
     *      - privilege - privilege name
     *      - assertion_class - the assertion class name
     */
    public function insertByPrivilegeName($record)
    {   
        $table = $this->getTable();
        $toInsert = array(
            'url' => $record['url'],
            'privilege_name' => $record['privilege'],
            'assertion_class' => $record['assertion_class']
        );  
        $pageAccessId = $table->insert($toInsert);
        return $pageAccessId;
    }


    /**
     * @param string $url 
     * @return string privilege name
     */
    public function getPrivilgeNameFromUrl($url)
    {   
        $table = $this->getTable();
        $select = $table->select();
        $select->where('url = ?', $url);

        $cache = $this->getCache();

        $cacheId = md5('Core_Model_UrlAccess_' . $url); 
        if (!$result = $cache->load($cacheId)) {
            $result = $table->fetchRow($select);
            $cache->save($result, $cacheId);
        }


        if ($result) {
            $result = $result->toArray();
            $privilegeName = $result['privilege_name'];
            return $privilegeName;
        }
    }

    /**
     * @param string $url
     * @return string assertion class name
     */
    public function getAssertionClass($url)
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where('url = ?', $url);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
            return $result['assertion_class'];
        }
        return;
    }

 


}

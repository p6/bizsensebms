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

class Core_Model_Lead_LeadSource extends Core_Model_Abstract
{
    /**
     * @var int lead source ID
     */
    protected $_leadSourceId;
   
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */ 
    protected $_dbTableClass = 'Core_Model_DbTable_LeadSource';

    public function __construct($leadSourceId = null)
    {
        if (is_numeric($leadSourceId)) {
            $this->_leadSourceId = $leadSourceId;
        }
        return $this;
    }

    /**
     * @param int $leadSourceId
     * @return fluent interface
     */
    public function setLeadSourceId($leadSourceId)
    {
        if (is_numeric($leadSourceId)) {
            $this->_leadSourceId = $leadSourceId;
        }
        return $this;
    }

    /**
     * @param array $data
     * @return int the newly created lead source ID
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $result = $table->insert($data);
        return $result;
    }

    /**
     * @return array the lead source record
     */
    public function fetch()
    {
        if (!is_numeric($this->_leadSourceId)){
            return false;
        }
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('lead_source'=>'lead_source'))
                ->where('lead_source_id = ?', $this->_leadSourceId);
        $result =  $table->fetchRow($select)->toArray();
        return $result;
    }

    /**
     * @param array $data
     * @return int 
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('lead_source_id = ?', $this->_leadSourceId);
        $result = $table->update($data, $where);
        return $result;
    }

    /**
     * @return int the number of records deleted
     */
    public function delete()
    {   
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('lead_source_id = ?', $this->_leadSourceId);
        $result = $table->delete($where);
        return $result;
    }
}

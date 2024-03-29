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

class Core_Model_Lead_Source extends BV_Model_Essential_Abstract
{
    protected $_leadSourceId;
   
    public function __construct($leadSourceId = null)
    {
        parent::__construct();
        if (is_numeric($leadSourceId)) {
            $this->_leadSourceId = $leadSourceId;
        }
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
    
    /*
     * @return Zend_Db_Select
     * Object to pass to Zend_Paginator
     */    
    public function getIndexSelectObject()
    {
        $select = $this->db->select();
        $select->from(array('ls'=>'lead_source'),
                array('*') );
        return $select;     
    }       
    
    /*
     * Fetch all the lead status entries
     */
    public function fetchAll()
    {
        $select = $this->getIndexSelectObject();
        $result = $this->db->fetchAll($select);
        return $result;     
    }
    
    /*
     * Fetch a single lead status item
     */
    public function fetch()
    {
        $select = $this->getIndexSelectObject();
        $select->where('lead_source_id = ?', $this->_leadSourceId);
        $result = $this->db->fetchRow($select);
        return $result;
    }
     
    public function fetchAsArray()
    {
        $select = $this->getIndexSelectObject();
        $select->where('lead_source_id = ?', $this->_leadSourceId);
        $result = $this->db->fetchRow($select, null, Zend_Db::FETCH_ASSOC);
        return $result;
    }

    /*
     * Delete the lead source
     */       
    public function delete()
    {
        $where = $this->db->quote($this->_leadSourceId, 'INTEGER');
        $result = $this->db->delete('lead_source', "lead_source_id = $where");
        return $result;
    } 
         
}



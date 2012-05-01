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


class Core_Model_Salutation
{
    public $db;

    protected $_salutation_id;

    public function __construct($salutationId)
    {
        $this->db = Zend_Registry::get('db');
        if (is_numeric($salutationId)) {
            $this->_salutation_id = $salutationId;
        }
    }
    
    /**
     * @param int $quoteId the invoice ID
     * @return fluent interface
     */
    public function setSalutationId($salutationId)
    {
        $this->_salutation_id = $salutationId;
        return $this;
    }

    public function fetch()
    {
        $select = $this->db->select();
        $select->from(array('s'=>'salutation'), array('salutation_id', 'name', 'description'));
        $select->where('salutation_id = ?', $this->_salutation_id);
        $result = $this->db->fetchRow($select);
        return $result;
    }    

    public function delete()
    {
        $where = $this->db->quote($this->_salutation_id, 'INTEGER');
        $result = $this->db->delete('salutation', "salutation_id = $where");
        return $result;
 
    }
}



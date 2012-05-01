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

class Core_Model_Quote_Item extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_QuoteItem';


    /**
     * @var object the Quote model
     */
    protected $_quoteModel;

    /**
     * @param object Core_Model_Quote
     * @return object Core_Model_Quote_Item
     */
    public function setQuoteModel($quoteModel)
    {
        $this->_quoteModel = $quoteModel;
        return $this;
    }

    /**
     * Delete all the items of the given quote
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('quote_id = ?', $this->_quoteModel->getQuoteId());
        $table->delete($where);
    }
    
    /**
     * Create a quote item
     * @param array $data with keys 
     * name, fa_group_id, opening_balance_type, opening_balance
     * @return int ledger ID 
     */
    public function create($data = array(),$quoteId)
    {
        $data['quote_id'] = $quoteId;
        return parent::create($data);
    }


}


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
class Core_Model_Finance_Receipt_Bank extends Core_Model_Abstract
{
    /**
     * @var the Receipt Bank ID
     */
    protected $_receiptBankId;
    
    const TO_TYPE = 'inoice to type';
    const TO_TYPE_ACCOUNT = 1;
    const TO_TYPE_CONTACT = 2;   

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_ReceiptBank';

    /**
     * @param receiptBankId
     */
     public function __construct($receiptBankId = null)
     {
        if (is_numeric($receiptBankId)) {  
            $this->_receiptBankId = $receiptBankId;
        }
        parent::__construct();
     }
    /**
     * @param int $receipBanktId
     * @return fluent interface
     */
    public function setReceipBanktId($receiptBankId)
    {
        $this->_receiptBankId = $receiptBankId;
        return $this;
    }

    /**
     * @return int the $receipBanktId
     */
    public function getReceiptBankId()
    {
        return $this->_receiptBankId;
    }


    /**
     * Create a finance group record
     * @param array $data with keys 
     * @return int Receipt bank ID 
     */
    public function create($data = array())
    {
        return parent::create($data);
    }
    
    /**
     * @param array $data and receiptId
     * @return bool
     */
    public function edit($data,$receiptId)
    {   
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('receipt_id = ?', $receiptId);
        return $table->update($data, $where);
    }
    
    /**
     * @param array $data
     * @return bool
     */
    public function update($data)
    {   
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('receipt_bank_id = ?', $this->_receiptBankId);
        return $table->update($data, $where);
    }
    
    /**
     * @param receiptId
     * @return array the receiptBank record
     */
    public function fetchByReceiptId($receiptId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('receipt_id = ?',$receiptId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * Fetches a single Receipt Bank record from db 
     * Based on currently set bankAccountId
     * @return array of Receipt Bank record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('receipt_bank_id = ?', $this->_receiptBankId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

}

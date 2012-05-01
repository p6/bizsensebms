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
class Core_Model_Finance_Payment_Tds extends Core_Model_Abstract
{
    /**
     * @var the payment Bank ID
     */
    protected $_paymentTdsId;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_PaymentTds';

    /**
     * @param paymentTdsId
     */
    public function __construct($paymentTdsId = null)
     {
        if (is_numeric($paymentTdsId)) {  
            $this->_paymentTdsId = $paymentTdsId;
        }
        parent::__construct();
     }
    /**
     * @param int $paymentTdsId
     * @return fluent interface
     */
    public function setPaymentTdsId($paymentTdsId)
    {
        $this->_paymentTdsId = $paymentTdsId;
        return $this;
    }

    /**
     * @return int the ledger ID
     */
    public function getPaymentTdsId()
    {
        return $this->_paymentTdsId;
    }


    /**
     * Create a finance Payment Bank
     * @param array $data with keys 
     * name, fa_group_id, opening_balance_type, opening_balance
     * @return int  PaymentTds ID 
     */
    public function create($data = array())
    {
      $paymentTdsId = parent::create($data);
      return $paymentTdsId;
    }
    
    /**
     * @param array $data with keys
     * @return int
     */
    public function edit($data)
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_id = ?', $data['payment_id']);
        return $table->update($data, $where);
    }
    
    /**
     * @param Payment Id
     * Feteches a record from the payment_bank table
     * @return result object from Zend_Db_Select object
     */
    public function fetchbyPaymentId($paymentId)
    {
        $table = $this->getTable();
        $select = $table->select()->where(
            'payment_id = ?', $paymentId
        );
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
}

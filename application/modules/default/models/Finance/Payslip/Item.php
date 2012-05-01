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
class Core_Model_Finance_Payslip_Item extends Core_Model_Abstract
{
    /**
     * @var the Payslip Item ID
     */
	 protected $_payslipItemId;
    
    /**
     * @param payslipItemId
     */
     public function __construct($payslipItemId = null)
     {
        if (is_numeric($payslipItemId)) {  
            $this->_payslipItemId = $payslipItemId;
        }
        parent::__construct();
     }

	/**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_PayslipItem';
    /**
     * Create a Payslip Item
     * @param array $data with keys 
     * @return int Payslip Field ID 
     */
    public function create($data = array())
    {
        $this->_payslipItemId = parent::create($data);
        return $this->_payslipItemId;
    }
    
    /**
     * @param payslip Id
     * @return array of Payslip Item details 
     */
    public function getItemByPayslipId($payslipId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('payslip_id = ?', $payslipId);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /** 
     * @param int payslip Id
     * Deletes rows in the payslip table with spicified Payslip Id
     * @return bool
     */
    public function deleteAllItemsByPayslipId($payslipId)
    {
       $table = $this->getTable();
       $where = $table->getAdapter()->quoteInto(
            'payslip_id = ?', $payslipId
       );
       $result = $table->delete($where);
       return $result;
    }
   
    
    
}
    

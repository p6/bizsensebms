<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation,  version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 */

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Finance_Payment_Bank extends Core_Model_Abstract
{
    /**
     * @var the payment Bank ID
     */
    protected $_paymentBankId;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_PaymentBank';

    /**
     * @param paymentBankId
     */
    public function __construct($paymentBankId = null)
     {
        if (is_numeric($paymentBankId)) {  
            $this->_paymentBankId = $paymentBankId;
        }
        parent::__construct();
     }
    /**
     * @param int $paymentBankId
     * @return fluent interface
     */
    public function setPaymentBankId($paymentBankId)
    {
        $this->_paymentBankId = $paymentBankId;
        return $this;
    }

    /**
     * @return int the ledger ID
     */
    public function getPaymentBankId()
    {
        return $this->_paymentBankId;
    }


    /**
     * Create a finance Payment Bank
     * @param array $data with keys 
     * name, fa_group_id, opening_balance_type, opening_balance
     * @return int  PaymentBank ID 
     */
    public function create($data = array())
    {
      $paymentBankId = parent::create($data);
      return $paymentBankId;
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
     * @param array $data
     * @return int
     */
    public function update($data)
    {   
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('payment_bank_id = ?', $this->_paymentBankId);
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
    
    /**
     * Fetches a single Payment Bank record from db 
     * Based on currently set bankAccountId
     * @return array of Payment Bank record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('payment_bank_id = ?', $this->_paymentBankId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
}

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

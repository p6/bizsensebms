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
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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
    

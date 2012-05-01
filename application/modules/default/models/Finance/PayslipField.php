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
class Core_Model_Finance_PayslipField extends Core_Model_Abstract
{
    const EARNING_FIELDS = '1';
    const DEDUCTION_TAX_FIELDS = '2';
    const DEDUCTION_NON_TAX_FIELDS = '3';
    /**
     * @var the Payslip Field ID
     */
	 protected $_payslipFieldId;
    
    /**
     * @param payslipFlieldId
     */
     public function __construct($payslipFieldId = null)
     {
        if (is_numeric($payslipFieldId)) {  
            $this->_payslipFlieldId = $payslipFieldId;
        }
        parent::__construct();
     }

	/**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_PayslipField';
    
    /**
     * @param int payslipFieldId
     * @return fluent interface
     */
    public function setPayslipFieldId($payslipFieldId)
    {
        $this->_payslipFieldId = $payslipFieldId;
        return $this;
    }

    /**
     * @return int payslipFieldId
     */
    public function getPayslipFieldId()
    {
        return $this->_payslipFieldId;
    }

    /**
     * Create a Payslip Field 
     * @param array $data with keys 
     * @return int Payslip Field ID 
     */
    public function create($data = array())
    {
        $payslipFieldData = array(
            'name' => $data['name'],
            'enabled' => $data['enabled'],
            'type' => $data['type'],
            'ledger_id' => $data['ledger_id'],
            'machine_name' => $data['machine_name']
        );
        
        $this->_payslipFieldId = parent::create($payslipFieldData);
        
        return $this->_payslipFieldId;
    }
    
    /** 
     * @return array enabled fields
     */
    public function getEnabledFields()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('enabled = ?', '1');
        $result = $table->fetchAll($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        
        $enabledFields = array();
        for ($i = 0; $i < count($result); $i++ ) {
            $enabledFields[$result[$i]['machine_name']] = '1';
        }
        
        return $enabledFields;
    }
    
    /** 
     * @param int type
     * @return array enabled fields by type
     */
    public function getEnabledFieldsByType($type)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('enabled = ?', '1')
                    ->where('type = ?' , $type);
        $result = $table->fetchAll($select);
        
        if ($result) {
            $result = $result->toArray();
        }             
        return $result;
    }
    
    /**
     * Fetches a single Payslip Field record from db 
     * Based on currently set payslip field id
     * @return array of payslip field record 
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('payslip_field_id = ?', $this->_payslipFieldId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /** 
     * @param int type
     * @return array type fields
     */
    public function getFieldsByType($type)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('type = ?', $type);
        $result = $table->fetchAll($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        
        return $result;
    }
    
    /** 
     * @return fluent interface
     */
    public function settings($data)
    {
        $table  = $this->getTable();
           
        $taxFieldRecord = $this->getFieldsByType(self::DEDUCTION_TAX_FIELDS); 
       
        for ($i = 0; $i < count($taxFieldRecord); $i++ ) {
            $fieldname = $taxFieldRecord[$i]['machine_name']."_ledger";
            if ($fieldname != '') {
               $ledgerData['ledger_id'] = $data[$fieldname];
               $where = $table->getAdapter()->quoteInto('machine_name  = ?', 
                                         $taxFieldRecord[$i]['machine_name']);
               $result = $table->update($ledgerData, $where);
            }
        }
        
        $nontaxFieldRecord = $this->getFieldsByType(
                                              self::DEDUCTION_NON_TAX_FIELDS);
        
        for ($i = 0; $i < count($nontaxFieldRecord); $i++ ) {
            $fieldname = $nontaxFieldRecord[$i]['machine_name']."_ledger";
            if ($fieldname != '') {
               $ledgerData['ledger_id'] = $data[$fieldname];
               $where = $table->getAdapter()->quoteInto('machine_name  = ?', 
                                         $nontaxFieldRecord[$i]['machine_name']);
               $result = $table->update($ledgerData, $where);
            }
        }
          
        foreach ($data as $name => $value) {
            $where = $table->getAdapter()->quoteInto('machine_name  = ?', $name);
            $dataToUpdate['enabled'] = $value;
            $result = $table->update($dataToUpdate, $where);
        }
        
    }
    
    /** 
     * @param int Payslip field machine name
     * @return int Payslip field Id
     */
    public function getFieldsIdByMachineName($machine_name)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('machine_name = ?', $machine_name);
        $result = $table->fetchRow($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        
        return $result['payslip_field_id'];
    }
    
    /** 
     * @param int Payslip field machine name
     * @return int Payslip field Id
     */
    public function getLedger()
    {
        $result = $this->fetchAll();
        $ledger = array();
        for ($i = 0; $i < count($result); $i++ ) {
           if($result[$i]['ledger_id'] != 0) {
               $ledgerName = $result[$i]['machine_name']."_ledger";
               $ledger[$ledgerName] = $result[$i]['ledger_id'];
           }
        }        
        return $ledger;
    }
    
    /** 
     * @param int Payslip field Id
     * @return string machine name
     */
    public function getMachineNameById($id)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('payslip_field_id  = ?', $id);
        $result = $table->fetchRow($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        return $result['machine_name'];
    }
    
    /** 
     * @param int Payslip field Id
     * @return string machine name
     */
    public function getNameById($id)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('payslip_field_id  = ?', $id);
        $result = $table->fetchRow($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        return $result['name'];
    }
    
    /** 
     * @param string machine name
     * @return int type
     */
    public function getTypeByMachineName($machineName)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('machine_name  = ?', $machineName);
        $result = $table->fetchRow($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        return $result['type'];
    }
    
    /**
     * @param int Machine name
     * Creates a row in the Indirect Expense ledger
     * @return int ledger entry ID 
     */
    public function getLedgerIdByMachineName($machineName)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('machine_name = ?', $machineName);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result['ledger_id'];
    }
    
    /** 
     * @param string machine name
     * @return int type
     */
    public function getTypeByName($name)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('name  = ?', $name);
        $result = $table->fetchRow($select);
        
        if ($result) {
            $result = $result->toArray();
        }
        return $result['type'];
    }
    
}
    

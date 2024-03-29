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
class Core_Model_Finance_Vendor extends Core_Model_Abstract
{    
    /**
     * @var the Vendor ID
     */
    protected $_vendorId;
    
    /**
     * The vendor type 
     */
     const VENDOR_TYPE_SUNDRY_CREDITOR = 1;
     const VENDOR_TYPE_OTHER = 2;
     
    public function __construct($vendorId = null)
    {
        if (is_numeric($vendorId)) {  
            $this->_vendorId = $vendorId;
        }
        parent::__construct();
    }
     
    /**
    * @see Core_Model_Abstract::_dbTableClass
    */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_Vendor';
     
    /**
     * @param int $vendorId
     * @return fluent interface
     */
    public function setVendorId($vendorId)
    {
        $this->_vendorId = $vendorId;
        return $this;
    }

    /**
     * @return int the Vendor ID
     */
    public function getVendorId()
    {
        return $this->_vendorId;
    }


    /**
     * Create a finance group record
     * @param array $data with keys 
     * @return int vendor ID 
     */
    public function create($data = array())
    {
        $this->_vendorId = parent::create($data);
        
        $log = $this->getLoggerService();
        $info = 'Vendor created with vendor id = '. $this->_vendorId;
        $log->info($info);
        
        return $this->_vendorId;
    }
    
    /**
     * Fetches a single vendor record from db 
     * Based on currently set vendorId
     * @return array of vendor record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('vendor_id = ?', $this->_vendorId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param array $data with keys 
     * @return int
     */
    public function edit($data)
    {
        $dataToUpdate = $this->unsetNonTableFields($data);
        $table  = $this->getTable();
        $where = $table->getAdapter()->quoteInto('vendor_id  = ?', 
                                                            $this->_vendorId);
        $result = $table->update($dataToUpdate, $where);
        
        $vendorRecord = $this->fetch();
        if ($vendorRecord['ledger_id'] != '') {
            $ledgerData['name'] = $vendorRecord['name'];
            $financeLedgerModel = 
                    new Core_Model_Finance_Ledger($vendorRecord['ledger_id']);
            $financeLedgerModel->edit($ledgerData);
        }
        
        $log = $this->getLoggerService();
        $info = 'Vendor edited with vendor id = '. $this->_vendorId;
        $log->info($info);
        
        return $result;
    }
    
    /**
     * deletes a row in table based on currently set vendorId
     * deletes respective ledger entry details
     * @return int
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('vendor_id = ?', 
                                                            $this->_vendorId);
        $result = $table->delete($where);
        
        $log = $this->getLoggerService();
        $info = 'Vendor deleted with vendor id = '. $this->_vendorId;
        $log->info($info);
        
        return $result;
    }
    
    /**
     * @return ledger Id 
     * If ledger is not exist, new ledger will be created
     * @return int ledger Id
     */
    public function getLedgerId()
    {
       $data = $this->fetch();
       if ($data['ledger_id'] == "") {
            $financeGroupModel = new Core_Model_Finance_Group;
            
            $ledgerDataToInsert = array (
                    'name' => $this->getName(),
                    'fa_group_id' => 
                        $financeGroupModel->getGroupIdByName('Sundry Creditors'),
                    'opening_balance_type' => '1',
                    'opening_balance' => '0',
                );
            $financeLedgerModel = new Core_Model_Finance_Ledger();
            $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
            $dataToUpdate['ledger_id'] = $financeLedgerId;
            $vendorId = $this->_vendorId;
            $this->edit($dataToUpdate);
            return $financeLedgerId;
       }
       return $data['ledger_id'];
    }
    
    /**
     * @return string name of the vendor
     */
    public function getName()
    {
        $vendorRecord = $this->fetch();    
        $name = $vendorRecord['name'];
        return $name;
    }
    
    /**
     * @param array $data with keys 
     * @return int initialize ledgerId
     */
    public function initializeLedger($data)
    {
        $financeGroupModel = new Core_Model_Finance_Group;
        $ledgerDataToInsert = array (
                    'name' => $this->getName(),
                    'fa_group_id' => 
                        $financeGroupModel->getGroupIdByName('Sundry Creditors'),
                    'opening_balance_type' => $data['opening_balance_type'],
                    'opening_balance' => $data['opening_balance'],
                );
         $financeLedgerModel = new Core_Model_Finance_Ledger();
         $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
         $dataToUpdate['ledger_id'] = $financeLedgerId;
         $vendorId = $this->_vendorId;
         $this->edit($dataToUpdate);
         return $financeLedgerId;
    }
    
     /**
      * @return bool
      */
    public function ledgerExists()
    {
        $vendorRecord = $this->fetch();
        if ($vendorRecord['ledger_id'] == "") {
            return 0;
        }
        else {
            return 1;
        }
    }
    
    /**
     * Fetches all vendor record from db 
     * @param int type
     * @return array of vendor records
     */
    public function fetchByType($type)
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

}



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
class Core_Model_Finance_CashAccount extends Core_Model_Abstract
{
    /**
     * @var the Cash account ID
     */
     protected $_cashAccountId;
    
     /**
     * @param cashAccountId
     */
     public function __construct($cashAccountId = null)
     {
        if (is_numeric($cashAccountId)) {  
            $this->_cashAccountId = $cashAccountId;
        }
        parent::__construct();
     }

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_CashAccount';
     
    /**
     * @param int cashAccountId
     * @return fluent interface
     */
    public function setCashAccountId($cashAccountId)
    {
        $this->_cashAccountId = $cashAccountId;
        return $this;
    }

    /**
     * @return int the Cash account ID
     */
    public function getCashAccountId()
    {
        return $this->_cashAccountId;
    }


    /**
     * Create a finance Cash Account record
     * @param array $data with keys 
     * name, fa_group_id, opening_balance_type, opening_balance
     * @return int cash account ID 
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $dataToInsert = array(
            'name' => $data['name'],
            'branch_id' => $data['branch_id'],
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId()
        );
        $financeGroupModel = new Core_Model_Finance_Group;
        $ledgerDataToInsert = array (
          'fa_group_id' => $financeGroupModel->getGroupIdByName('Cash In Hand'),
          'name' => $data['name'],
          'opening_balance_type' => $data['opening_balance_type'],
          'opening_balance' => $data['opening_balance']
        );
        $financeLedgerModel = new Core_Model_Finance_Ledger();
        $financeLedgerId = $financeLedgerModel->create($ledgerDataToInsert);
        
        $dataToInsert['fa_ledger_id'] = $financeLedgerId;
        $this->_cashAccountId = $table->insert($dataToInsert);
                
        $log = $this->getLoggerService();
        $info = 'Cash account created with cash account id = '.
                                                        $this->_cashAccountId;
        $log->info($info);
        return $this->_cashAccountId;
    }
    
    /**
     * @return int the Cash Ledger ID
     */
    public function getLedgerId()
    {
       $data = $this->fetch();
       return $data['fa_ledger_id'];
    }
    
    /**
     * @return string name
     */
    public function getName()
    {
       $data = $this->fetch();
       return $data['name'];
    }
    
    
    /**
     * Fetches a single Cash account record from db 
     * Based on currently set bankAccountId
     * @return array of Cash account record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)
                    ->where('cash_account_id = ?', $this->_cashAccountId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param array $data with keys
     * updates Cash account details and ledger entries
     * @return int
     */
    public function edit($data)
    {
        $table = $this->getTable();
        $dataToUpdate = array(
            'name' => $data['name'],
            'branch_id' => $data['branch_id'],
            'created' => time(),
            'created_by' => $this->getCurrentUser()->getUserId()
        );
        $where = $table->getAdapter()->quoteInto('cash_account_id  = ?' ,
                                                         $this->_cashAccountId);
        $result = $table->update($dataToUpdate, $where);
        
        $cashAccountRecord = $this->fetch();
        $ledgerId = $cashAccountRecord['fa_ledger_id'];
        $ledgerData['name'] = $data['name'];
        $financeLedgerModel = new Core_Model_Finance_Ledger($ledgerId);
        $financeLedgerModel->edit($ledgerData);
        
        $log = $this->getLoggerService();
        $info = 'Cash account edited with cash account id = '.
                                                        $this->_cashAccountId;
        $log->info($info);
        return $result;
    }
    
    /**
     * deletes a row in table based on currently set cashAccountId
     * deletes respective ledger entry details
     * @return int
     */
    public function delete()
    {
        $this->_previousCashAccountData = $this->fetch();
        
        $cashaccountRecord = $this->fetch();
        $ledgerId = $cashaccountRecord['fa_ledger_id'];
        $financeLedgerModel = new Core_Model_Finance_Ledger($ledgerId);
        $financeLedgerModel->delete();
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('cash_account_id = ?',
                                                         $this->_cashAccountId);
        $result = $table->delete($where);
        
        $log = $this->getLoggerService();
        $info = 'Cash account deleted with cash account id = '.
                                                        $this->_cashAccountId;
        $log->info($info);
        return $result;
    }

}



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
class Core_Model_Tax_Type extends Core_Model_Abstract
{
    protected $_taxTypeId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_TaxType';

    public function __construct($taxTypeId = null)
    {
        parent::__construct();
        if (is_numeric($taxTypeId)){
            $this->_taxTypeId = $taxTypeId;
        }
        $this->db = Zend_Registry::get('db');
    } 


    /**
     * @param int $taxTypeId
     * @return string tax name
     * @TODO deprecated. Use getName() instead
     */
    public function getTaxNameFromId($taxTypeId = null)
    {
        return $this->db->fetchOne('SELECT name FROM tax_type WHERE tax_type_id = ?', $taxTypeId);
    }     

    /**
     * @param int taxTypeId
     * @return fluent interface
     */

    public function setTaxTypeId($taxTypeId)
    {
        $this->_taxTypeId = $taxTypeId;
        return $this;
    }
    
    /**
     * @TODO deprecated. Use getPercentage() instead
     */
    public function getTaxPercentageFromId($taxTypeId = null)
    {
        return $this->db->fetchOne('SELECT percentage FROM tax_type WHERE tax_type_id = ?', $taxTypeId);
    }     


    /**
     * Fetch the tax type item 
     * @return array tax type record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('tax_type_id = ?', $this->_taxTypeId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * Create a tax type 
     * @param array $data
     * @return int the tax type ID
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        
        $dataToinsertTaxTable = array (
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'percentage' => $data['percentage']
                    );
        $this->_taxTypeId = $table->insert($dataToinsertTaxTable);

        $ledger = new Core_Model_Finance_Ledger;
        $accountGroup = new Core_Model_Finance_Group;

        
        $dataToinsert = array (
                    'name' => $data['name'],
                    'fa_group_id' => 
                        $accountGroup->getGroupIdByName('Duties And Taxes'),
                    'opening_balance_type' => 
                        $data['opening_balance_type'],
                    'opening_balance' => $data['opening_balance'],
                );
        $ledgerId = $ledger->create($dataToinsert);
        $where = $table->getAdapter()->quoteInto('tax_type_id = ?', $this->_taxTypeId);
        $dataToUpdate = array(
            'fa_ledger_id' => $ledgerId
        );
        $table->update($dataToUpdate, $where);
        return $this->_taxTypeId;
    }

    /**
     * Edit a  tax type
     * @param array $data
     * @return bool 
     */
    public function edit($data = array())
    {
        $table = $this->getTable(); 
        $data = $this->unsetNonTableFields($data);
        $where = $table->getAdapter()->quoteInto('tax_type_id = ?', $this->_taxTypeId);
        $result =  $table->update($data, $where); 
        $ledgerId = $this->getLedgerId();
        $ledgerModel = new Core_Model_Finance_Ledger;
        $ledgerModel->setLedgerId($ledgerId);
        $data = array(
            'name' => $this->getName()
        );
        $ledgerEditResult = $ledgerModel->edit($data);
        return $result;
    }
    
    /**
     * Delete a tax type
     * return bool
     */        
    public function delete()
    {
        $table = $this->getTable(); 
        $where = $table->getAdapter()->quoteInto('tax_type_id = ?', $this->_taxTypeId);
        $ledgerId = $this->getLedgerId();
        $result = $table->delete($where);
        $ledgerModel = new Core_Model_Finance_Ledger();
        $ledgerModel->setLedgerId($ledgerId);
        $ledgerDeleteResult = $ledgerModel->delete();
        return $result;
    }

    /**
     * @return Zend_Db_Table object so that Zend_Paginator can use it   
     * @development requires review before release
     */
    public  function getTaxTypes()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select();
        $select->from(array('tt'=>'tax_type'),
                    array('tax_type_id','name', 'percentage','description'));
        return $select;
    }


    /**
     * @return float tax percentage
     */
    public function getPercentage()
    {
       $taxData = $this->fetch();
       return $taxData['percentage'];
    }

    /**
     * @return int ledger ID of the tax type
     */
    public function getLedgerId()
    {
       $record = $this->fetch();
       return $record['fa_ledger_id'];
    }

    /**
     * @return string name of the tax
     */
    public function getName()
    {
       $record = $this->fetch();
       return $record['name'];
    }

}



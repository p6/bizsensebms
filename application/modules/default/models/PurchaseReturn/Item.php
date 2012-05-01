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
class Core_Model_PurchaseReturn_Item extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_PurchaseReturnItem';
    /**
     * @var object the purchaseReturn model
     */
    protected $_purchaseReturnModel;

    /**
     * @param object Core_Model_purchaseReturn
     * @return object Core_Model_purchaseReturn_Item
     */
    public function setPurchaseReturnModel($purchaseReturnModel)
    {
        $this->_purchaseReturnModel = $purchaseReturnModel;
        return $this;
    }
    
    /**
     * Create a purchase Rreturn record
     * @param array $data with keys 
     * @return int ledger ID 
     */
    public function create($data = array(),$purchaseReturnId)
    {
        $data['purchase_return_id'] = $purchaseReturnId;
        return parent::create($data);
    }
    
    /**
     * @param array $data and purchase Return Id
     * @return bool
     */
    public function edit($data,$purchaseReturnId)
    {
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('purchase_return_id = ?', $purchaseReturnId);
        return $table->update($data, $where);
    }
    
    /**
     * Delete all the items of the given purchaseReturn
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('purchase_return_id = ?', $this->_purchaseReturnModel->getpurchaseReturnId());
        $table->delete($where);
    }


}


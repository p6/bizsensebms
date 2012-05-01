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

class Core_Model_Finance_Purchase_ItemOthers extends Core_Model_Abstract
{
    /**
     * @var the purchaseItem ID
     */
    protected $_purchaseItemOthersId;
    
    const TO_TYPE_ACCOUNT = 1;
    const TO_TYPE_CONTACT = 2;   

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Finance_PurchaseItemOthers';

    /**
     * @param purchaseItemOthersId
     */
     public function __construct($purchaseItemOthersId = null)
     {
        if (is_numeric($purchaseItemOthersId)) {  
            $this->_purchaseItemOthersId = $purchaseItemOthersId;
        }
        parent::__construct();
     }
     
    /**
     * @var object the purchase model
     */
    protected $_purchaseModel;
    
    /**
     * @param int $purchaseItemOthersId
     * @return fluent interface
     */
    public function setPurchaseItemOthersId($purchaseItemOthersId)
    {
        $this->_purchaseItemOthersId = $purchaseItemOthersId;
        return $this;
    }

    /**
     * @return int the purchase Item ID
     */
    public function getPurchaseItemOthersId()
    {
        return $this->_purchaseItemOthersId;
    }


    /**
     * Create a finance Purchase Item
     * @param array $data with keys and purchase id
     * @return int Purchase Item ID 
     */
    public function create($data = array(), $purchaseId)
    {
        $data['purchase_id'] = $purchaseId;
        if ($data['tax_type_id'] == '') {
            $data['tax_type_id'] = null;
        } 
        return parent::create($data);
    }
    
    /**
     * @param array $data with keys and purchaseId
     * @return bool
     */
    public function edit($data,$purchaseId)
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('purchase_id = ?', $purchaseId);
        return $table->update($data, $where);
    }
    
    /**
     * @param object Core_Model_Purchase
     * @return object Core_Model_PurchaseItem
     */
    public function setPurchaseModel($purchaseModel)
    {
        $this->_purchaseModel = $purchaseModel;
        return $this;
    }

    /**
     * Delete all the items of the given purchase Item
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('purchase_id = ?', 
                                        $this->_purchaseModel->getPurchaseId());
        $table->delete($where);
    }

}

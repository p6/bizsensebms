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

class Core_Model_SalesReturn_Item extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_SalesReturnItem';


    /**
     * @var object the salesReturn model
     */
    protected $_salesReturnModel;

    /**
     * @param object Core_Model_salesReturn
     * @return object Core_Model_salesReturn_Item
     */
    public function setSalesReturnModel($salesReturnModel)
    {
        $this->_salesReturnModel = $salesReturnModel;
        return $this;
    }
    
    /**
     * Create a Sales Rreturn record
     * @param array $data with keys 
     * @return int ledger ID 
     */
    public function create($data = array(),$salesReturnId)
    {
        $data['sales_return_id'] = $salesReturnId;
        return parent::create($data);
    }
    
    /**
     * @param array $data and Sales Return Id
     * @return bool
     */
    public function edit($data,$salesReturnId)
    {
        
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('sales_return_id = ?', $salesReturnId);
        return $table->update($data, $where);
    }
    
    /**
     * Delete all the items of the given salesReturn
     */
    public function deleteAll()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('sales_return_id = ?', $this->_salesReturnModel->getSalesReturnId());
        $table->delete($where);
    }


}


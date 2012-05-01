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

class Model_Purchase
{
    public $db;
    public $purchaseId;

    public function __construct($purchaseId = null)
    {
        $this->db = Zend_Registry::get('db');
        $this->purchaseId = $purchaseId;    
    }
    public function getDetails()
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();
        $select->from(array('p'=>'purchase'),
                        array('*'))
                ->joinLeft(array('a'=>'account'),
                    'p.vendorId = a.accountId',
                    array('a.accountName'))
                ->joinLeft(array('b'=>'branch'),
                    'b.branchId = p.consigneeBranch', array('b.branchName'))
                ->joinLeft(array('c'=>'contact'),
                        'p.vendorContactId = c.contactId', array('c.firstName as vendorContact'))
                ->joinLeft(array('u'=>'user'), 
                        'u.uid = p.createdBy', array('u.email'))
                ->where('p.id = ?', $this->purchaseId);
        $result = $db->fetchRow($select);
        
        return $result; 
    }
    
    public function getItemDetails()
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();
        $select->from(array('pp'=>'purchaseProducts'),
                        array('*'))
                ->joinLeft(array('p'=>'purchase'),
                    'pp.purchaseId = p.id')
                ->joinLeft(array('pr'=>'product'), 
                    'pp.productId = pr.productId', array('pr.productName'))
                ->joinLeft(array('tt'=>'taxType'),
                    'tt.id = pp.productId', array('tt.name as taxName', 'tt.percentage'))
                ->where('pp.purchaseId = ?', $this->purchaseId);
 
        $sql = $select->__toString();
        $result = $db->fetchAll($select);
        
        return $result; 
 
    }

    /**
     * Initiate index search and sort processing object
     * @return Zend_Db_Select object
     */
    public function getListingSelectObject($search, $sort)
    {
        $process = new Purchase_Index;
        $processed = $process->getListingSelectObject($search, $sort);
        return $processed;
    }


}



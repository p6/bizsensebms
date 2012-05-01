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

class Core_Model_Finance_BankAccount_Index extends Core_Model_Index_Abstract
{
    /*
     * Process search form and sort values 
     * @return Zend_Db_Select object
     * Which can be passed to Zend_Paginator
     */
    public function getPaginator()
    {
        $db = Zend_Registry::get('db');
        
        $table = $this->_model->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false);
        
        $sort = $this->_sort;
        $search = $this->_search;
         /*
         * Sort data    
         */
        switch ($sort) {

            case "accountNoAsc" :
                $select->order('account_no');
            break;
            case "accountNoDes" :
                $select->order('account_no DESC');
            break;
            
            case "bankNameAsc" :
                $select->order('bank_name');
            break;
            case "bankNameDes" :
                $select->order('bank_name DESC');
            break;
        }
        
        /**
         * Search 
         */  
        if ($search['date'] != '') {
            $accountNo = $search['account_no'];
            $select->where('account_no = ?',$accountNo);
        }   
        
        if ($search['bank_name'] != '') {
            $accountName = $search['bank_name'];
            $select->where('bank_name = ?',$accountName);
        }   
        
        $paginator = 
        new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }

}

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

class Core_Model_Quote_Report_DateRange extends Core_Model_Index_Abstract
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

            case "subjectAsc" :
                $select->order('subject');
            break;
            case "subjectDes" :
                $select->order('subject DESC');
            break;

            case "createdAsc" :
                $select->order('created');
            break;
            case "createdDes" :
                $select->order('created DESC');
            break;
           
            case 'accountAsc':
                $select->order('account_id');           
                break;
            case 'accountDes':
                $select->order('account_id DESC');
                break;

            case 'contactAsc':
                $select->order('contactId');           
                break;
            case 'contactDes':
                $select->order('contactId DESC');
                break;

            case 'branchAsc':
                $select->order('branchId');           
                break;
            case 'branchDes':
                $select->order('branchId DESC');
                break;

            case 'assignedtoAsc':
                $select->order('assigned_to');           
                break;
            case 'assignedtoDes':
                $select->order('assigned_to DESC');
                break;

        }
        /**
         * Search 
         */  
       
        if ($search['from'] != '' && $search['to'] != '') {
            
            $fromdate = new Zend_Date($search['from'], 'yyyy.MM.dd');
            $from = $fromdate->getTimestamp();
                        
            $todate = new Zend_Date($search['to'], 'yyyy.MM.dd');
            $to = $todate->getTimestamp();
           
            $select->where("date BETWEEN '$from' and '$to'");
        }
        
        if ($search['assigned_to'] != '') {
            $assignTo =  $search['assigned_to'];
            $select->where("assigned_to = ?", $assignTo);
        }
        
        if ($search['account_id'] != '') {
            $accountId =  $search['account_id'];
            $to_type = 1;
            $select->where("to_type = ?", $to_type);
            $select->where('to_type_id = ?', $accountId);
        }
        
        if ($search['contact_id'] != '') {
            $contactId =  $search['contact_id'];
            $to_type = 2;
            $select->where("to_type = ?", $to_type);
            $select->where('to_type_id = ?', $contactId);
        }
        
        if (isset($search['status'])) {
            $status =  $search['status'];
            $select->where("status = ?", $status);
        }
        
        $paginator = 
        new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }

 
}

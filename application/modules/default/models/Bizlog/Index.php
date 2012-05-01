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

class Core_Model_Bizlog_Index extends BV_Model_Index_Abstract
{

    /**
     * @return Zend_Paginator object
     */
    public function getPaginator()
    {
        $table = $this->_model->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);            
        
        /**
         * search
         */
        $search = $this->_search;
        if(!(empty($search['date_from'])) and !(empty($search['date_to']))) {
            $startFromDate = $search['date_from'] . $search['start_time'] . '+05:30';
            $startToDate = $search['date_to'] . $search['end_time'] . '+05:30';
            $select->where("log_timestamp between '$startFromDate' and '$startToDate'");
        } 
      
        $sort = $this->_sort;
        /** 
         * Sort data    
         */
        switch ($sort) {

            case 'timestampAsc' :
                $select->order('log_timestamp');
                break;
            case 'timestampDes' :
                $select->order('log_timestamp DESC');
                break;

            case 'priorityAsc' :
                $select->order('priority');
                break;
            case 'priorityDes' :
                $select->order('priority DESC');
                break;

            case 'messageAsc' :
                $select->order('message');
                break;
            case 'messageDes' :
                $select->order('message DESC');
                break;

            case 'priority_nameAsc' :
                $select->order('priority_name');
                break;
            case 'priority_nameDes' :
                $select->order('priority_name DESC');
                break;

            default:
                $select->order('log_timestamp DESC');
                break;
        }
        
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
 
    }
}


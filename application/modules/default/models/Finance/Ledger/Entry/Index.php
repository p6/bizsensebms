<?php
/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation,  version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Finance_Ledger_Entry_Index extends Core_Model_Index_Abstract
{
    public function getPaginator()
    {
        $table = $this->_model->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false);
        $select->join('fa_ledger', 
                    'fa_ledger.fa_ledger_id = fa_ledger_entry.fa_ledger_id',
                    array()
                );
        $select->where('fa_ledger.fa_ledger_id = ?', $this->_model->getLedgerId());
        
        $sort = $this->_sort;
        $search = $this->_search;
        /**
         * Sort data    
         */
        switch ($sort) {

            case "dateAsc" :
                $select->order('transaction_timestamp');
            break;
            case "dateDes" :
                $select->order('transaction_timestamp DESC');
            break;
            
        }
        /**
         * Search 
         */
                
        if ($search['from'] != '' && $search['to'] != '') {
            $fromdate = new Zend_Date($search['from']);
            $from = $fromdate->getTimestamp();
                        
            $todate = new Zend_Date($search['to']);
            $to = $todate->getTimestamp();
           
            $select->where("transaction_timestamp BETWEEN '$from' and '$to'");
        }
        
        if ($search['notes'] != '') {
            $notes = $search['notes'];
            $select->where('notes like ?','%' .$notes .'%' );
        }
        
        $variableModel = new Core_Model_Variable('finance_year_start_date');
        $startDateElement = $variableModel->getValue();
                
        $variableModel = new Core_Model_Variable('finance_year_end_date');
        $endDateElement = $variableModel->getValue();
       
        if ($startDateElement != '' && $endDateElement != '') {
            $startDate = new Zend_Date($startDateElement);
            $startDate = $startDate->getTimestamp();
            
            $endDate = new Zend_Date($endDateElement);
            $endDate = $endDate->getTimestamp();
            $select->where(
                   "transaction_timestamp BETWEEN '$startDate' and '$endDate'");
        }
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }

}

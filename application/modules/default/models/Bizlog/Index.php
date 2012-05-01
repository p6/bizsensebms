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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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


<?php
/**
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
 * You can contact Binary Vibes Information Technologies Pvt. 
 * Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
class Core_Model_Quote_Report_ContactAccount extends Core_Model_Index_Abstract
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
        
        $paginator = 
        new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }

 
}
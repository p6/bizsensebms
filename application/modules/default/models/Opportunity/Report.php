<?php
/*
 * Opportunity report
 *
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Opportunity_Report
{
    public $db;

    public function __construct($opportunityId = null)
    {
        $this->db = Zend_Registry::get('db');
    }

    /*
     * Generates Zend_Db_Select object from the input
     * @param array of user input
     */
    public function generateSelectObject($input = array())
    {
        /* 
         * Construct the select object
         */
        $opportunityModel = new Core_Model_Opportunity;
        $select = $opportunityModel->getfetchAllSelectObject();
        
        /*
          * Filter the user input date
          */
         if ($input['created_from'] != '') {
            $dateFrom = new Zend_Date($input['created_from']);
            $createdFrom = $dateFrom->getTimestamp();
            $select->where("o.created > ?",$createdFrom);
         } 
         
         if($input['created_to'] != '') {
            $dateTo = new Zend_Date($input['created_to']);
            $createdTo = $dateTo->getTimestamp();
            $select->where("o.created < ?",$createdTo);
         }
       
         if ($input['expected_close_date_from'] != '') {
            $dateFrom = new Zend_Date($input['expected_close_date_from']);
            $createdFrom = $dateFrom->getTimestamp();
            $select->where("expected_close_date > ?",$createdFrom);
         } 
         
         if($input['expected_close_date_to'] != '') {
            $dateTo = new Zend_Date($input['expected_close_date_to']);
            $createdTo = $dateTo->getTimestamp();
            $select->where("expected_close_date < ?",$createdTo);
         }
       
        /*
         * Add where clauses using branchId input element 
         */
        $branchId = $input['branch_id'];         
        $expression = '';
        $count = 0;
        if ($branchId) {
            foreach ($branchId as $value){
                if (is_numeric($value)) {
                    $count++;
                    if ($count > 1) {
                        $expression .= " or o.branch_id = $value";  
                    } else {
                        $expression .= "  o.branch_id = $value";  
                    }
                } 
            }
        }
        if ($count > 0) {
            $select->where($expression);
        }    

        /*
         * Add where clauses using userId input element
         */
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($input['assigned_to'])) {
            $assignedTo = User::getUserIdFromEmail($input['assigned_to']);
        }
       
        $assignedTo = $input['assigned_to']; 
        if (is_numeric($assignedTo)) {
            $select->where('l.assigned_to = ?', $assignedTo);
        }
        return $select;         
    }
}

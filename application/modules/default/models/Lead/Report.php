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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/**
 * Lead report
 */
class Core_Model_Lead_Report
{
    public $db;

    public function __construct($leadId = null)
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
        $select = $this->db->select();
        $select->from(array('l' => 'lead'),
                  array('lead_id', 'first_name', 'last_name', 'company_name', 'created', 'updated'))
               ->joinLeft(array('ls'=>'lead_source'),
                            'l.lead_source_id = ls.lead_source_id', array('ls.name'=>'name'))
               ->joinLeft(array('lst'=>'lead_status'),
                            'l.lead_status_id = lst.lead_status_id', array('lst.name'=>'name'))
               ->joinLeft(array('p'=>'profile'),
                            'p.user_id = l.assigned_to', array('p.first_name as asssignedToFirstName'))
               ->joinLeft(array('b'=>'branch'),
                            'b.branch_id = l.branch_id', array('b.branch_name'));

         
         if (!$input) {
            return $select;         
         }
         /*
          * Filter the user input date
          */
         if ($input['createdFrom'] != '') {
            $dateFrom = new Zend_Date($input['createdFrom']);
            $createdFrom = $dateFrom->getTimestamp();
            
            $select->where("created > ?",$createdFrom);
         } 
         
         if($input['createdTo'] != '') {
            $dateTo = new Zend_Date($input['createdTo']);
            $createdTo = $dateTo->getTimestamp();
            $select->where("created < ?",$createdTo);
         }
       
         if ($input['lastUpdatedFrom'] != '') {
            $dateFrom = new Zend_Date($input['lastUpdatedFrom']);
            $createdFrom = $dateFrom->getTimestamp();
            $select->where("lastUpdated > ?",$createdFrom);
         } 
         
         if($input['lastUpdatedTo'] != '') {
            $dateTo = new Zend_Date($input['lastUpdatedTo']);
            $createdTo = $dateTo->getTimestamp();
            $select->where("lastUpdated < ?",$createdTo);
         }
         
        /*
         * Add where clauses using the converted input element
         */        
        $converted = $input['converted'];
        if (sizeof($converted) > 0)  {
            /*
             * Both converted and not converted
             */
            if ( ( in_array('converted', $converted) ) and ( in_array('notConverted', $converted) )) {
                $select->where("l.converted = 1 or l.converted = 0"); 
            } else 
            
            /*
             * Converted only
             */
            if (in_array('converted', $converted)) {
                $select->where('l.converted like ?', '%' . '1' . '%'); 
            } else 
            
            /*
             * Not converted only
             */
            if (in_array('notConverted', $converted)) {
               $select->where('l.converted like ?', '%' . '0' . '%'); 
            }
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
                        $expression .= " or l.branch_id = $value";  
                    } else {
                        $expression .= "  l.branch_id = $value";  
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
            $assignedTo = Core_Model_User::getUserIdFromEmail($input['assigned_to']);
        }
        
        if (isset($assignedTo) && (is_numeric($assignedTo)) ) {
            $select->where('l.assigned_to = ?', $assignedTo);
        }
        return $select;         
    }
}

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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Branch_Index extends BV_Model_Essential_Abstract
{

    /*
     * Process search form and sort values 
     * @return Zend_Db_Select object
     * Which can be passed to Zend_Paginator
     */
    public function getListingSelectObject($search, $sort)
    {
        $acl = Zend_Registry::get('acl');

        $select = $this->db->select();
        $select->from(array('b'=>'branch'), 
                    array('branch_id', 'branch_name', 'city', 'phone'))
                ->joinLeft(array('u'=>'user'),
                    'u.user_id = b.branch_manager', array('u.email'=>'email as branchManagerEmail'))
                ->joinLeft(array('pb'=>'branch'),
                    'pb.branch_id = b.parent_branch_id', array('pb.branch_name'=>'branch_name as parentBranchName'))
                ;

        /* 
         * Sort data    
         */

        switch ($sort) {
            case "branchNameAsc" :
                $select->order('b.branch_name ASC');
                break;

            case "branchNameDes" :
                $select->order('b.branch_name DESC');
                break;

            case "cityAsc" :
                $select->order('b.city ASC');
                break;

            case "cityDes" :
                $select->order('b.city DESC');
                break;

            case "parentBranchAsc" :
                $select->order('pb.branch_name ASC');
                break;

            case "parentBranchDes" :
                $select->order('pb.branch_name DESC');
                break;
            default:
                break;

        }

        /*
         * Search 
         */
       return $select; 
    }
 
}

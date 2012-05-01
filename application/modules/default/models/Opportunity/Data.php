<?php
/*
 * Opportunity data in various formats
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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Opportunity_Data extends BV_Model_Essential_Abstract
{

    /**
     * Generate data in Dojo JSON format
     * @return Zend_Dojo_Data
     * Lits of users to whom the lead can be assigned
     */
    public function getAssignedToDojoData()
    {
        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');
        $data = new Core_Model_User_Data;        
        $dojoData = null;
        $assignToAny = $acl->isAllowed($user,'assign opportunities to any user');
        $assignToBranch = $acl->isAllowed($user,'assign opportunities to own branch users');
        $assignToOwnRole = $acl->isAllowed($user,'assign opportunities to own role users');
       
        /**
         * Anded expression
         */ 
        $assignToBranchAndRole = ($assignToBranch and $assignToOwnRole);

        /**
         * Add where clauses to the select object bassed on permissions
         */
        if ($assignToAny) {
            $dojoData = $data->getAllDojoData();
        } elseif ($assignToBranchAndRole) {
            $dojoData = $data->getAllDojoData();
        } elseif ($assignToBranch) {
            $dojoData = $data->getOwnBranchDojoData();
        } else if ($assignToOwnRole) {
            $dojoData = $data->getOwnRoleDojoData();
        } else {
            $dojoData = $data->getOwnDojoData();
        }

        return $dojoData;
    } 
    
    /**
     * Generate data in Dojo JSON format
     * @return Zend_Dojo_Data
     * Lits of users to whom the lead can be assigned
     */
    public function getAssignedToBranchDojoData()
    {
        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');
 
        $select = $this->db->select();
        $select->from(array('b'=>'branch'), 
                    array('branch_id', 'branch_name'))
                ->joinLeft(array('p'=>'profile'),
                    'p.user_id = p.user_id', array() );

        $assignToAny = $acl->isAllowed($user, 'assign opportunities to any user');
        $assignToBranch = $acl->isAllowed($user, 'assign opportunities to own branch users');
        $assignToOwnRole = $acl->isAllowed($user, 'assign opportunities to own role users');
       
        /**
         * Anded expression
         */ 
        $assignToBranchAndRole = ($assignToBranch and $assignToOwnRole);

        /**
         * Add where clauses to the select object bassed on permissions
         */
        if ($assignToAny) {
        } else {
            $select->where('b.branch_id = ?', User_Current::getBranchId());
        }
 
        $select->group('b.branch_id');    
        $items = $this->db->fetchAll($select, null, Zend_Db::FETCH_ASSOC);

        $data = new Zend_Dojo_Data('branch_id', $items);

        return $data;
    } 

}

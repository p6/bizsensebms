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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Account_Index extends Core_Model_Index_Abstract
{

    /*
     * Process search form and sort values 
     * @return Zend_Db_Select object
     * Which can be passed to Zend_Paginator
     */
    public function getPaginator()
    {
        $sort = $this->_sort;
        $search = $this->_search;

        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');

        $table = $this->_model->getTable();

        $select = $table->select()->setIntegrityCheck(false);
        
        $select->from($table,
                    array(
                        'account_id', 
                        'account_name', 
                        'billing_city', 
                        'phone', 
                        'assigned_to'
                    )
                )
               ->joinLeft(
                    array('u'=>'user'), 
                    'u.user_id = account.assigned_to', 
                    array('u.email')
                )
               ->joinLeft(
                    array('b'=>'branch'), 
                    'b.branch_id = account.branch_id', 
                    array('b.branch_name')
                )
               ->joinLeft(
                    array('p'=>'profile'), 
                    'p.user_id = account.assigned_to', 
                    null
                );

        /**
         * Sort data    
         */
        switch ($sort) {

            case "accountNameAsc" :
                $select->order('account_name');
            break;
            case "accountNameDes" :
                $select->order('account_name DESC');
            break;

            case "firstNameDes" :
                $select->order('account_name DESC');
            break;

            case "billingCityAsc" :
                $select->order('billing_city');
            break;

            case "billingCityDes" :
                $select->order('billing_city DESC');
            break;

            case "companyNameAsc" :
                $select->order('company_name');
            break;
    
            case "companyNameDes" :
                $select->order('company_name DESC');
            break;

            case "emailAsc" :
                $select->order('email');
            break;
            case "emailDes" :
                $select->order('email DESC');
            break;

            case "mobileAsc" :
                $select->order('mobile');
            break;
            case "mobileDes" :
                $select->order('mobile DESC');
            break;

            case 'phoneAsc' :
                $select->order('phone');
            break;
            case 'phoneDes' :
                $select->order('phone DESC');
            break;

            default:
                $select->order('account_id DESC');

        }

        /**
         * Search 
         */
        $name = $search['accountName'];
        $select->where('account.account_name like ?', '%' . $name . '%');


        $assignedTo = $search['assignedTo'];

        if (is_numeric($assignedTo)) {    
            $select->where('account.assigned_to like ?', '%' . $assignedTo . '%');
        }
    
        if (isset($search['branch_id'])) {
            $branchId = $search['branch_id'];
        } else {
            $branchId = '';
        }    

        $city = $search['city'];
        $select->where('account.billing_city like ?', '%' . $city . '%');    

        $assignedTo = $search['assignedTo'];
        if (is_numeric($assignedTo)) {
            $select->where('account.assigned_to = ?',$assignedTo);
        }

        if (is_numeric($branchId)) {
            $select->where('account.branch_id = ?', $branchId);
        }


        /**
         * Apply ACLs
         */
        if ($acl->isAllowed($user, 'view all accounts')) {
        } elseif ($acl->isAllowed($user, 'view own branch accounts')) {
            $select->where('account.branch_id = ?', $user->getBranchId());
        } elseif ($acl->isAllowed($user, 'view own role accounts')) {
            $select->where('p.primaryRole = ?', $user->getPrimaryRoleId());
        } elseif ($acl->isAllowed($user, 'view own accounts')) {
            $select->where('account.assigned_to = ?', $user->getUserId());
        } else {
            $select->where('1>2');
        }

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
 
}

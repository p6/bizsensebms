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
class Core_Model_Contact_Index extends Core_Model_Index_Abstract
{

    /**
     * Process search form and sort values 
     * @return Zend_Db_Select object
     * Which can be passed to Zend_Paginator
     */
    public function getPaginator() 
    {
        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');
        $db = Zend_Registry::get('db');

        $sort = $this->_sort;
        $search = $this->_search;
        
        $select = $db->select();
        $select->from(array('c' => 'contact'),
                  array('first_name', 'middle_name', 'last_name',
             'account_id', 'assigned_to', 'branch_id','contact_id','work_email',
             'work_phone'))
            ->joinLeft(array('a'=>'account'),
                'c.account_id = a.account_id', array('a.account_name'=>'account_name as account_name'));
        /* 
         * Sort data    
         */

        switch ($sort) {
            case "first_nameAsc" :
                $select->order('c.first_name');
            break;
            case "first_nameDes" :
                $select->order('c.first_name DESC');
            break;

            case "last_nmeAsc" :
                $select->order('c.last_name');
            break;
            case "last_nameDes" :
                $select->order('c.last_name DESC');
            break;

            case "companyNameAsc" :
                $select->order('c.company_name');
            break;
            case "companyNameDes" :
                $select->order('c.company_name DESC');
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

            default:
                $select->order('c.contact_id DESC');
        }

        /*
         * Search 
         */
        $name = $search['name'];
        $name = $db->quote("%$name%");

        $select->where("c.first_name like $name or c.middle_name like $name or c.last_name like $name");

        if (isset($search['assigned_to'])) {
            $assignedTo = $search['assigned_to'];
        } else {
            $assignedTo = '';
        }

        if (is_numeric($assignedTo)) {
            $select->where('c.assigned_to = ?', $assignedTo);
        }

        if ($search['branch_id']) {
            $select->where('c.branch_id = ?', $search['branch_id']);
        } 
        
        $city = $search['city'];
        $select->where('c.billing_city like ?', '%'.$city.'%');
        
        /**
         * Apply access controls to the select object
         */
        if ($acl->isAllowed($user, 'view all contacts')) {
            $select->where('c.assigned_to like ?', '%' . '' . '%');
        } else if ($acl->isAllowed($user, 'view own branch contacts')) {
            $select->where('c.branch_id = ?', $user->getBranchId());
        } else {
            $select->where('c.assigned_to = ?', $user->getUserId());
        }
        $select->orWhere("c.assigned_to = ''");
        
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        return $paginator;
    }
 
}

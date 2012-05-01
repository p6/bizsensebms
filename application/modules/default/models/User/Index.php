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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_User_Index extends Core_Model_Index_Abstract
{

    /**
     * @see Core_Model_Index_Abstract::getPaginator()
     */
    public function getPaginator()
    {
        $search = $this->_search;
        $sort = $this->_sort;
        $db = Zend_Registry::get('db');

        $acl = Zend_Registry::get('acl');
        $select = $db->select();
        $select->from(array('u'=>'user'), 
                    array('user_id', 'email', 'status'))
                ->joinLeft(array('p'=>'profile'),
                    'p.user_id = u.user_id', 
                        array('p.first_name', 'p.middle_name', 'p.last_name'));


        if ($search['email'] != '') {
            $select->where('u.email like ?', '%' . $search['email'] . '%');
        }
        
        if ($search['name'] != '') {
            $select->where('p.first_name like ?', '%' . $search['name'] . '%');
            $select->orWhere('p.middle_name like ?', '%' . $search['name'] . '%');
            $select->orWhere('p.last_name like ?', '%' . $search['name'] . '%');
        }
       
        /** 
         * Sort data    
         */
        switch ($sort) {
            case "emailAsc" :
                $select->order('u.email');
            break;
            case "emailDes" :
                $select->order('u.email DESC');
            break;
            
            case 'statusAsc':
                $select->order('u.status');
            case 'statusDes':
                $select->order('u.status DESC');
            
            case 'firstNameAsc':
                $select->order('p.first_name');
            case 'firstNameDes':
                $select->order('p.first_name DESC');

            case 'middleNameAsc':
                $select->order('p.middle_name');
            case 'middleNameDes':
                $select->order('p.middle_name DESC');

            case 'lastNameAsc':
                $select->order('p.last_name');
            case 'lastNameDes':
                $select->order('p.last_name DESC');

        }


        /**
         * Search 
         * Yet to be implemented
         */
        $paginator = new Zend_Paginator(
            new Zend_Paginator_Adapter_DbSelect($select)
        );

        return $paginator; 
    }
 
}

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
 */

/* @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

Class Core_Model_Newsletter_Subscriber_Index extends Core_Model_Index_Abstract
{
    /**
     * @return Zend_Paginator object
     */
    public function getPaginator()
    {
        $db = Zend_Registry::get('db');

        $table = $this->_model->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false);
  
        $search = $this->_search; 
        $sort = $this->_sort;
        
        /**
         * Sort data    
         */
        switch ($sort) {

            case "firstNameAsc" :
                $select->order('first_name');
            break;
            case "firstNameDes" :
                $select->order('first_name DESC');
            break;

            case "emailDes" :
                $select->order('email DESC');
            break;

            case "emailAsc" :
                $select->order('email');
            break;
        }
        
        /**
         * Search 
         */  
        if ($search['email']) {
            $select->Where('email like ?', '%' . $search['email'] . '%');
        }   
        if ($search['name']) {
            $select->orWhere('first_name like ?', '%' . $search['name'] . '%');
            $select->OrWhere('middle_name like ?', '%' . $search['name'] . '%');
            $select->orWhere('last_name like ?', '%' . $search['name'] . '%');
        }  
                
        if ($search['format']) {
            $select->where('format = ?',$search['format']);
        }  
        
        if ($search['format'] == '0') {
            $select->where('format = ?',0);
        }   
        
        if ($search['status']) {
            $select->where('status = ?',$search['status']);
        }   
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
}

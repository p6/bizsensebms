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

Class Core_Model_Campaign_Index extends Core_Model_Index_Abstract
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
        $select->join('user',
                    'user.user_id =  campaign.assigned_to',
                    array('user.email as assigned_name')
                );

        $search = $this->_search;
        $sort = $this->_sort;

        /** 
         * Sort data    
         */
        switch ($sort) {

            case 'nameAsc' :
                $select->order('name');
                break;
            case 'nameDes' :
                $select->order('name DESC');
                break;
            case 'start_dateAsc' :
                $select->order('start_date');
                break;
            case 'start_dateDes' :
                $select->order('start_date DESC');
                break;
            case 'assigned_toAsc' :
                $select->order('assigned_to');
                break;
            case 'assigned_toDes' :
                $select->order('assigned_to DESC');
                break;
        }

        /**
         * Search 
         */
        $name = $search['name'];
        $name = $db->quote("%$name%");
        $select->where("name like $name");

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
}
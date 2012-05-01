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
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
class Core_Model_Branch_Data extends BV_Model_Essential_Abstract
{

    /*
     * Zend_Db_Select object
     */
    protected $_select;

    /*
     * Initiliaze the Zend_Db_Select_Object
     */
    public function __construct()
    {
        parent::__construct();
        $select = $this->db->select();
        $select->from(array('b'=>'branch'),
                 array('branch_id', 'branch_name'));
        $this->_select = $select;
 
    }

    /*
     * @return Zend_Dojo_Data
     */
    public function getItems()
    {
        $items = $this->db->fetchAll($this->_select, null, Zend_Db::FETCH_ASSOC);
        $data = new Zend_Dojo_Data('branch_id', $items);
        return $data;
    }

    /*
     * @return Zend_Dojo_Data
     * Only self user data
     */
    public function getOwnDojoData()    
    {
        $this->_select->where('b.branchId = ?', User_Current::getBranchId());
        $data = $this->getItems();  
        return $data;
    }

    /*
     * @return Zend_Dojo_Data
     * All user data
     */
    public function getAllDojoData()
    {
        return $this->getItems();
    }
}

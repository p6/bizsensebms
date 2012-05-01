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

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */


class Model_Warehouse
{
    public $db;
    public $warehouseId;

    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    public function getIndex()
    {
        $select = $this->db->select();
      
        $select->from(array('w'=>'warehouse'), 
                        array('id', 'name', 'city', 'phone'))
                ->joinLeft(array('b'=>'branch'),
                    'b.branchId = w.branchId', array('b.branchName'))
                ->joinLeft(array('u'=>'user'),
                    'w.incharge = u.uid', array('u.email')); 
        return $select; 
    }
    
    public function get()
    {
        $select = $this->db->select();
      
        $select->from(array('w'=>'warehouse'), 
                        array('*'))
                ->joinLeft(array('b'=>'branch'),
                    'b.branchId = w.branchId', array('b.branchName'))
                ->joinLeft(array('u'=>'user'),
                    'w.incharge = u.uid', array('u.email')) 
                ->where('w.id = ?', $this->warehouseId); 
        $result = $this->db->fetchRow($select);
        return $result;
    }
    
    public function getAll()
    {
    }
}



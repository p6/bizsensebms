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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_User_Data extends Core_Model_Abstract
{

    /**
     * @var Zend_Db_Select object
     */
    protected $_select;

    /**
     * Initiliaze the Zend_Db_Select_Object
     */
    public function __construct()
    {
        parent::__construct();
        $db = Zend_Registry::get('db');
        $this->db = $db;
        $select = $this->db->select();
        $select->from(array('u'=>'user'),
                 array('user_id', 'email'))
                ->joinLeft(array('p'=>'profile'),
                    'p.user_id = u.user_id', array())
                ->order('u.user_id');
        $this->_select = $select;
 
    }

    /**
     * @return Zend_Dojo_Data
     */
    public function getItems()
    {
        $items = $this->db->fetchAll($this->_select, null, Zend_Db::FETCH_ASSOC);
        $data = new Zend_Dojo_Data('user_id', $items);
        return $data;
    }

    /**
     * @return Zend_Dojo_Data
     * Only self user data
     */
    public function getOwnDojoData()    
    {
        $this->_select->where('u.user_id = ?', $this->getCurrentUser()->getUserId());
        $data = $this->getItems();  
        return $data;
    }

    /**
     * @return Zend_Dojo_Data
     * Only own role user data
     */
    public function getOwnRoleDojoData()    
    {
        
        $this->_select->where('p.primary_role = ?', $this->getCurrentUser()->getPrimaryRoleId());
        return $this->getItems();
    }

    /**
     * @return Zend_Dojo_Data
     * Only own branch user data
     */
    public function getOwnBranchDojoData()    
    {
        $this->_select->where('p.branch_id = ?', $this->getCurrentUser()->getBranchId());
        return $this->getItems();
    }

    /**
     * @return Zend_Dojo_Data
     * All user data
     */
    public function getAllDojoData()
    {
        return $this->getItems();
    }
}

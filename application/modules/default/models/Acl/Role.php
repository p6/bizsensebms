<?php
/**
 * DEPRECATED USE Core_Model_Role instead
 * User roles
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
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Acl_Role extends BV_Model_Essential_Abstract
{

    protected $_roleId;
    
    public function __construct($roleId = null)
    {
        parent::__construct();
        if (is_numeric($roleId) and $roleId > 3) {
            $this->_roleId = $roleId;
        } 
    }

    /*
     * Set the roleId
     */
    public function setId($roleId = null)
    {
        if (is_numeric($roleId) and $roleId > 3) {
            $this->_roleId = $roleId;
        } 
    }

    /*
     * Fetch a single role record
     */    
    public function fetch()
    {
       $result = $this->db->fetchRow('SELECT * FROM role WHERE role_id = ?', $this->_roleId);  
       return $result; 
    }
    
    /*
     * Delete the role
     */
    public function delete()
    {
        $where = $this->db->quote($this->_roleId, 'INTEGER');
        $result = $this->db->delete('role', "role_id = $where");
        return $result;
    }
    
}



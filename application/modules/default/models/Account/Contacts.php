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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Account_Contacts extends BV_Model_Essential_Abstract
{

    /**
     * @property the account id to operate on
     */
    protected $_account_id;

    /**
     * @param int $accountId the account id to operate on
     */
    public function __construct($accountId)
    {
        if (is_numeric($accountId)) {
            $this->_account_id = $accountId;
        }
        parent::__construct();
    }

    /**
    * @return object with a list of contacts belonging to this account
    */
    public function getContacts()  
    {
        $db = Zend_Registry::get('db');
        $select = $db->select();
        $select->from(array('c'=>'contact'), array('c.*'))
                ->where('c.account_id = ?', $this->_account_id);  
        $result = $this->db->fetchAll($select);
        return $result;
    }
}

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
class Core_Model_User_Current extends BV_Model_Essential_Abstract
{
    private static $_id;
    private static $_branchId;
    private static $_primaryRoleId;
 

    /**
     * @return current user's ID
     */
    public static function getId()
    {
        if (!is_numeric(self::$_id)) {
            $auth =  Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $identity = $auth->getIdentity();
                $db = Zend_Registry::get('db');
                              
                $validator = new Zend_Validate_EmailAddress();
                if ($validator->isValid($identity)) {
                    $uid = $db->fetchOne(
                        'SELECT user_id FROM user WHERE email = ?', $identity);
                } else {
                   $uid = $db->fetchOne(
                    'SELECT user_id FROM user WHERE username = ?', $identity);
                }
                self::$_id = $uid;
            }
        }
        return self::$_id;
    }


    /**
     * @return BranchId of the current user
     */
    public static function getBranchId()
    {
        if (!is_numeric(self::$_branchId)) {
                $db = Zend_Registry::get('db');
                $uid = self::getId();
                $branchId = $db->fetchOne('SELECT branch_id FROM profile WHERE user_id = ?', $uid);
                self::$_branchId = $branchId;
        }
        return self::$_branchId;
    }

    /**
     * @return primary role id of the current user
     */
    public static function getPrimaryRoleId()
    {
        if (!is_numeric(self::$_primaryRoleId)) {
                $db = Zend_Registry::get('db');
                $uid = self::getId();
                $primaryRoleId = $db->fetchOne('SELECT primary_role FROM profile WHERE user_id = ?', $uid);
                self::$_primaryRoleId = $primaryRoleId;
        }
        return self::$_primaryRoleId;
    }
     
}

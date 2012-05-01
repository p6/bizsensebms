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

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_User_Acl_CanAccess
{
    public function __construct($value)
    {
        $this->_userId = $value;
    }

    public function assert(Bare_Acl $acl)
    {
        $value = $this->_userId;
        $access = false;
        $user = $acl->getSubject();

        if (
            !(
                is_array($value) 
                and array_key_exists('user_id', $value) 
                and is_numeric($value['user_id'])
            ) 
        ) {
            return false;
        }

        if ($user->getUserId() == $value['user_id']) {
            $access = true;
        } else if ($acl->isAllowed($user,'administer users')) {
            $access = true;
        } 

	    if ($access) {
            return true;
	    } else {
           return false;
	    }
    }
}


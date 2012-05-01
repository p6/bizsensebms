<?php
/*
 * Validate whether the contact can be accessed
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
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

class Contact_Validate_CanAccess extends Zend_Validate_Abstract
{
    const MSG = 'msg';

    protected $_messageTemplates = array(
        self::MSG => "Contact cannot be accessed",
    );

    public $db;

    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    public function isValid($value)
    {
		$acl = new Acl;
        $this->_setValue($value);
		if(is_array($value)) {
			$contactArray = $value;
			$value = $contactArray['contactId'];
		}
		$model = new Contact($value);
		$contact = $model->fetch();
		if(!empty($contact)) {
			if($acl->hasAccessTo('view all contacts')) {
				return true;
			} elseif($acl->hasAccessTo('view own branch contacts')) {
				if ($contact->branchId == User::getCurrentUserBranchId()){
				return true;
				}
            } elseif ($acl->hasAccessTo('view own role contacts')) {
                $assignedTo = $contact->assignedTo;
                $assignedToUser = new User($assignedTo);
                $assignedToUserData = $assignedToUser->fetch();
                if ($assignedToUserData->primaryRole == User_Current::getPrimaryRoleId()) {
                   return true; 
                }
			} elseif ($acl->hasAccessTo('view own contacts')) {
				if ($contact->assignedTo == User::getCurrentUserId()) {
				return true;
				}
			} else {
	    		$this->_error(self::MSG);
    	    	return false;
			}
    	}
	}
}

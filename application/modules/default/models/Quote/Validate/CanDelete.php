<?php
/*
 * Validate whether the quote can be deleteed by the current user
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

class Core_Model_Quote_Validate_CanDelete extends Zend_Validate_Abstract
{
    const MSG = 'msg';

    protected $_messageTemplates = array(
        self::MSG => "Quote does not exist",
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
			$quoteArray = $value;
			$value = $quoteArray['quoteId'];
		}

		$model = new Quote($value);
		$quote = $model->fetch();

		if(!empty($quote)) {
			if($acl->hasAccessTo('delete all quotes')) {
				return true;
			} elseif($acl->hasAccessTo('delete own branch quotes')) {
				if ($quote->branchId == Core_Model_User_Current::getBranchId()){
				return true;
				}
			} elseif ($acl->hasAccessTo('delete own role quotes')) {
                $currentUserRole = Core_Model_User_Current::getPrimaryRoleId();
                $assignedTo = $quote->assigned_to;
                $assignedToUser = new User($assignedTo);
                $assignedToUserData = $assignedToUser->fetch();
                $assignedToUserRole = $assignedToUserData->primaryRole;
                if ($assignedToUserRole == $currentUserRole) {
                    return true;
                }
            } elseif ($acl->hasAccessTo('delete own quotes')) {
				if ($quote->assignedTo == Core_Model_User_Current::getId()) {
				return true;
				}
			} else {
	    		$this->_error(self::MSG);
    	    	return false;
			}
    	}
        
        return false;
	}
}

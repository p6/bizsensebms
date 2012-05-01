<?php
/**
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
class Core_Model_Account_Acl_CanEdit 
    extends Core_Model_Account_Acl_Abstract
    implements Bare_Acl_AssertInterface
{

    public function assert(Bare_Acl $acl)
    {
        $user = $acl->getSubject();
        $user->getBranchId();
		$accountRecord = $this->_accountRecord;
		if(!empty($accountRecord)) {
			if ($acl->isAllowed($user, 'edit all accounts')) {
				return true;
			} elseif($acl->isAllowed($user,'edit own branch accounts')) {
			        
				if ($accountRecord->branch_id == $user->getBranchId()){
				    return true;
				}
            } elseif ($acl->isAllowed($user,'edit own role accounts')) {
                $assignedTo = $account->assigned_to;
                $assignedToUser = new User($assignedTo);
                $assignedToUserData = $assignedToUser->fetch();
                if ($assignedToUserData->primary_role == $user->getPrimaryRoleId()) {
                   return true; 
                }
			} elseif ($acl->isAllowed($user,'edit own accounts')) {
				if ($accountRecord->assigned_to == $user->getUserId()) {
				return true;
				}
			} else {
    	    	return false;
			}
    	} 
    }
}

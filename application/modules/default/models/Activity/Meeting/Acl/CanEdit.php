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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/** 
 * Validate whether the meeting can be edited
 */
class Core_Model_Activity_Meeting_Acl_CanEdit extends Core_Model_Activity_Meeting_Acl_Abstract
{

    public function assert(Bare_Acl $acl)
    {
        $user = $acl->getSubject();
       	$meetingRecord = $this->_meetingRecord;
		if(!empty($meetingRecord)) {
			if($acl->isAllowed($user, 'edit all meetings')) {
				return true;
			} elseif($acl->isAllowed($user, 'edit own branch meetings')) {
				if ($meetingRecord->branch_id == $user->getBranchId()){
				return true;
				}
            } elseif ($acl->isAllowed($user, 'edit own role meetings')) {
                $assignedTo = $meetingRecord->assigned_to;
                $assignedToUser = new Core_Model_User($assignedTo);
                $assignedToUserData = $assignedToUser->fetch();
                if ($assignedToUserData->primary_role == $user->getPrimaryRoleId()) {
                   return true; 
                }
			} elseif ($acl->isAllowed($user, 'edit own meetings')) {
				if ($meetingRecord->assigned_to == $user->getUserId()) {
				return true;
				}
			} else {
    	    	return false;
			}
    	}
	}
}

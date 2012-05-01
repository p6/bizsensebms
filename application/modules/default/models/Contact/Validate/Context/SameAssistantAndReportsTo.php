<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
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

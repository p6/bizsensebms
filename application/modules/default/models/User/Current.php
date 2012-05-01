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

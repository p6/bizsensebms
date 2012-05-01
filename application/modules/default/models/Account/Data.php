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

class Core_Model_Account_Data extends BV_Model_Essential_Abstract
{

    /*
     * Generate data in Dojo JSON format
     * @return Zend_Dojo_Data
     * Lits of users to whom the lead can be assigned
     */
    public function getAssignedToDojoData()
    {
        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');
        $data = new Core_Model_User_Data;        
        $dojoData = null;
        $assignToAny = $acl->isAllowed($user,'assign accounts to any user');
        $assignToBranch = $acl->isAllowed($user,'assign accounts to own branch users');
        $assignToOwnRole = $acl->isAllowed($user,'assign accounts to own role users');
       
        /*
         * Anded expression
         */ 
        $assignToBranchAndRole = ($assignToBranch and $assignToOwnRole);

        /*
         * Add where clauses to the select object bassed on permissions
         */
        if ($assignToAny) {
            $dojoData = $data->getAllDojoData();
        } elseif ($assignToBranchAndRole) {
            $dojoData = $data->getAllDojoData();
        } elseif ($assignToBranch) {
            $dojoData = $data->getOwnBranchDojoData();
        } else if ($assignToOwnRole) {
            $dojoData = $data->getOwnRoleDojoData();
        } else {
            $dojoData = $data->getOwnDojoData();
        }

        return $dojoData;
    } 
    
    /*
     * Generate data in Dojo JSON format
     * @return Zend_Dojo_Data
     * Lits of users to whom the lead can be assigned
     */
    public function getAssignedToBranchDojoData()
    {
        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');
        $data = new Core_Model_Branch_Data;        
        $dojoData = null;
        $assignToAny = $acl->isAllowed($user,'assign accounts to any user');
        
        /*
         * Add where clauses to the select object bassed on permissions
         */
        if ($assignToAny) {
            $dojoData = $data->getAllDojoData();
        } else {
            $dojoData = $data->getOwnDojoData();
        }
        return $dojoData;
    } 

}

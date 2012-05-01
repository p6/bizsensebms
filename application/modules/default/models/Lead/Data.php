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

class Core_Model_Lead_Data extends BV_Model_Essential_Abstract
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
             
       
        $select = $this->db->select();
        $select->from(array('u'=>'user'), 
                    array('email', 'user_id'))
                ->joinLeft(array('p'=>'profile'),
                    'u.user_id = p.user_id', array() );

        $assignToAny = $acl->isAllowed($user, 'assign leads to any user');
        $assignToBranch = $acl->isAllowed($user, 'assign leads to own branch users');
        $assignToOwnRole = $acl->isAllowed($user, 'assign leads to own role users');
       
        /*
         * Anded expression
         */ 
        $assignToBranchAndRole = ($assignToBranch and $assignToOwnRole);

        /*
         * Add where clauses to the select object bassed on permissions
         */
        if ($assignToAny) {
        } elseif ($assignToBranchAndRole) {
            $select->where('p.primaryRole = ?', $user->getPrimaryRoleId());
            $select->orWhere('p.branch_id = ?', $user->getBranchId());
        } elseif ($assignToBranch) {
            $select->where('p.branch_id = ?', $user->getBranchId());
        } else if ($assignToOwnRole) {
            $select->where('p.primaryRole = ?', $user->getPrimaryRoleId());
        } else {
           $select->where('p.user_id = ?', $user->getUserId());
        }
 
        $items = $this->db->fetchAll($select, null, Zend_Db::FETCH_ASSOC);

        $data = new Zend_Dojo_Data('user_id', $items);
        $data->setLabel('name');

        return $data;
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
       
        $select = $this->db->select();
        $select->from(array('b'=>'branch'), 
                    array('branch_id', 'branch_name'))
                ->joinLeft(array('p'=>'profile'),
                    'p.user_id = p.user_id', array() );

        $assignToAny = $acl->isAllowed($user, 'assign leads to any user');
        $assignToBranch = $acl->isAllowed($user, 'assign leads to own branch users');
        $assignToOwnRole = $acl->isAllowed($user, 'assign leads to own role users');
       
        /*
         * Anded expression
         */ 
        $assignToBranchAndRole = ($assignToBranch and $assignToOwnRole);

        /*
         * Add where clauses to the select object bassed on permissions
         */
        if ($assignToAny) {
        } else {
            $select->where('b.branch_id = ?', $user->getBranchId());
        }
 
        $select->group('b.branch_id');    
        $items = $this->db->fetchAll($select, null, Zend_Db::FETCH_ASSOC);

        $data = new Zend_Dojo_Data('branch_id', $items);

        return $data;
    } 

}

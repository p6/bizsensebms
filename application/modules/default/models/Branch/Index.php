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

class Core_Model_Branch_Index extends BV_Model_Essential_Abstract
{

    /*
     * Process search form and sort values 
     * @return Zend_Db_Select object
     * Which can be passed to Zend_Paginator
     */
    public function getListingSelectObject($search, $sort)
    {
        $acl = Zend_Registry::get('acl');

        $select = $this->db->select();
        $select->from(array('b'=>'branch'), 
                    array('branch_id', 'branch_name', 'city', 'phone'))
                ->joinLeft(array('u'=>'user'),
                    'u.user_id = b.branch_manager', array('u.email'=>'email as branchManagerEmail'))
                ->joinLeft(array('pb'=>'branch'),
                    'pb.branch_id = b.parent_branch_id', array('pb.branch_name'=>'branch_name as parentBranchName'))
                ;

        /* 
         * Sort data    
         */

        switch ($sort) {
            case "branchNameAsc" :
                $select->order('b.branch_name ASC');
                break;

            case "branchNameDes" :
                $select->order('b.branch_name DESC');
                break;

            case "cityAsc" :
                $select->order('b.city ASC');
                break;

            case "cityDes" :
                $select->order('b.city DESC');
                break;

            case "parentBranchAsc" :
                $select->order('pb.branch_name ASC');
                break;

            case "parentBranchDes" :
                $select->order('pb.branch_name DESC');
                break;
            default:
                break;

        }

        /*
         * Search 
         */
       return $select; 
    }
 
}

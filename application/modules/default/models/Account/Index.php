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

class Core_Model_Account_Index extends Core_Model_Index_Abstract
{

    /*
     * Process search form and sort values 
     * @return Zend_Db_Select object
     * Which can be passed to Zend_Paginator
     */
    public function getPaginator()
    {
        $sort = $this->_sort;
        $search = $this->_search;

        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');

        $table = $this->_model->getTable();

        $select = $table->select()->setIntegrityCheck(false);
        
        $select->from($table,
                    array(
                        'account_id', 
                        'account_name', 
                        'billing_city', 
                        'phone', 
                        'assigned_to'
                    )
                )
               ->joinLeft(
                    array('u'=>'user'), 
                    'u.user_id = account.assigned_to', 
                    array('u.email')
                )
               ->joinLeft(
                    array('b'=>'branch'), 
                    'b.branch_id = account.branch_id', 
                    array('b.branch_name')
                )
               ->joinLeft(
                    array('p'=>'profile'), 
                    'p.user_id = account.assigned_to', 
                    null
                );

        /**
         * Sort data    
         */
        switch ($sort) {

            case "accountNameAsc" :
                $select->order('account_name');
            break;
            case "accountNameDes" :
                $select->order('account_name DESC');
            break;

            case "firstNameDes" :
                $select->order('account_name DESC');
            break;

            case "billingCityAsc" :
                $select->order('billing_city');
            break;

            case "billingCityDes" :
                $select->order('billing_city DESC');
            break;

            case "companyNameAsc" :
                $select->order('company_name');
            break;
    
            case "companyNameDes" :
                $select->order('company_name DESC');
            break;

            case "emailAsc" :
                $select->order('email');
            break;
            case "emailDes" :
                $select->order('email DESC');
            break;

            case "mobileAsc" :
                $select->order('mobile');
            break;
            case "mobileDes" :
                $select->order('mobile DESC');
            break;

            case 'phoneAsc' :
                $select->order('phone');
            break;
            case 'phoneDes' :
                $select->order('phone DESC');
            break;

            default:
                $select->order('account_id DESC');

        }

        /**
         * Search 
         */
        $name = $search['accountName'];
        $select->where('account.account_name like ?', '%' . $name . '%');


        $assignedTo = $search['assignedTo'];

        if (is_numeric($assignedTo)) {    
            $select->where('account.assigned_to like ?', '%' . $assignedTo . '%');
        }
    
        if (isset($search['branch_id'])) {
            $branchId = $search['branch_id'];
        } else {
            $branchId = '';
        }    

        $city = $search['city'];
        $select->where('account.billing_city like ?', '%' . $city . '%');    

        $assignedTo = $search['assignedTo'];
        if (is_numeric($assignedTo)) {
            $select->where('account.assigned_to = ?',$assignedTo);
        }

        if (is_numeric($branchId)) {
            $select->where('account.branch_id = ?', $branchId);
        }


        /**
         * Apply ACLs
         */
        if ($acl->isAllowed($user, 'view all accounts')) {
        } elseif ($acl->isAllowed($user, 'view own branch accounts')) {
            $select->where('account.branch_id = ?', $user->getBranchId());
        } elseif ($acl->isAllowed($user, 'view own role accounts')) {
            $select->where('p.primaryRole = ?', $user->getPrimaryRoleId());
        } elseif ($acl->isAllowed($user, 'view own accounts')) {
            $select->where('account.assigned_to = ?', $user->getUserId());
        } else {
            $select->where('1>2');
        }

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
 
}

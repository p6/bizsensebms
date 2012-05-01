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

class Core_Model_Contact_SelfService extends Core_Model_Index_Abstract
{

    /**
     * Process search form and sort values 
     * @return Zend_Db_Select object
     * Which can be passed to Zend_Paginator
     */
    public function getPaginator() 
    {
        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');
        $db = Zend_Registry::get('db');

        $sort = $this->_sort;
        $search = $this->_search;
        
        $select = $db->select();
        $select->from(array('c' => 'contact'),
                  array('first_name', 'middle_name', 'last_name',
             'account_id', 'assigned_to', 'branch_id','contact_id', ))
            ->joinLeft(array('a'=>'account'),
                'c.account_id = a.account_id', array('a.account_name'=>'account_name as account_name'));
        /* 
         * Sort data    
         */

        switch ($sort) {
            case "first_nameAsc" :
                $select->order('c.first_name');
            break;
            case "first_nameDes" :
                $select->order('c.first_name DESC');
            break;

            case "last_nmeAsc" :
                $select->order('c.last_name');
            break;
            case "last_nameDes" :
                $select->order('c.last_name DESC');
            break;

            case "companyNameAsc" :
                $select->order('c.company_name');
            break;
            case "companyNameDes" :
                $select->order('c.company_name DESC');
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

            default:
                $select->order('c.contact_id DESC');
        }

        /*
         * Search 
         */
        
         if ($search['self_service'] != null) {
            $selfService = $search['self_service'];
            $select->where('c.ss_enabled = ?', $selfService);
         }
         else {
            $select->where('c.ss_enabled = ?', '1');
         }
         

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        return $paginator;
    }
 
}

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

class Core_Model_User_Index extends Core_Model_Index_Abstract
{

    /**
     * @see Core_Model_Index_Abstract::getPaginator()
     */
    public function getPaginator()
    {
        $search = $this->_search;
        $sort = $this->_sort;
        $db = Zend_Registry::get('db');

        $acl = Zend_Registry::get('acl');
        $select = $db->select();
        $select->from(array('u'=>'user'), 
                    array('user_id', 'email', 'status'))
                ->joinLeft(array('p'=>'profile'),
                    'p.user_id = u.user_id', 
                        array('p.first_name', 'p.middle_name', 'p.last_name'));


        if ($search['email'] != '') {
            $select->where('u.email like ?', '%' . $search['email'] . '%');
        }
        
        if ($search['name'] != '') {
            $select->where('p.first_name like ?', '%' . $search['name'] . '%');
            $select->orWhere('p.middle_name like ?', '%' . $search['name'] . '%');
            $select->orWhere('p.last_name like ?', '%' . $search['name'] . '%');
        }
       
        /** 
         * Sort data    
         */
        switch ($sort) {
            case "emailAsc" :
                $select->order('u.email');
            break;
            case "emailDes" :
                $select->order('u.email DESC');
            break;
            
            case 'statusAsc':
                $select->order('u.status');
            case 'statusDes':
                $select->order('u.status DESC');
            
            case 'firstNameAsc':
                $select->order('p.first_name');
            case 'firstNameDes':
                $select->order('p.first_name DESC');

            case 'middleNameAsc':
                $select->order('p.middle_name');
            case 'middleNameDes':
                $select->order('p.middle_name DESC');

            case 'lastNameAsc':
                $select->order('p.last_name');
            case 'lastNameDes':
                $select->order('p.last_name DESC');

        }


        /**
         * Search 
         * Yet to be implemented
         */
        $paginator = new Zend_Paginator(
            new Zend_Paginator_Adapter_DbSelect($select)
        );

        return $paginator; 
    }
 
}

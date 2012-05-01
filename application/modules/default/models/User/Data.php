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

class Core_Model_User_Data extends Core_Model_Abstract
{

    /**
     * @var Zend_Db_Select object
     */
    protected $_select;

    /**
     * Initiliaze the Zend_Db_Select_Object
     */
    public function __construct()
    {
        parent::__construct();
        $db = Zend_Registry::get('db');
        $this->db = $db;
        $select = $this->db->select();
        $select->from(array('u'=>'user'),
                 array('user_id', 'email'))
                ->joinLeft(array('p'=>'profile'),
                    'p.user_id = u.user_id', array())
                ->order('u.user_id');
        $this->_select = $select;
 
    }

    /**
     * @return Zend_Dojo_Data
     */
    public function getItems()
    {
        $items = $this->db->fetchAll($this->_select, null, Zend_Db::FETCH_ASSOC);
        $data = new Zend_Dojo_Data('user_id', $items);
        return $data;
    }

    /**
     * @return Zend_Dojo_Data
     * Only self user data
     */
    public function getOwnDojoData()    
    {
        $this->_select->where('u.user_id = ?', $this->getCurrentUser()->getUserId());
        $data = $this->getItems();  
        return $data;
    }

    /**
     * @return Zend_Dojo_Data
     * Only own role user data
     */
    public function getOwnRoleDojoData()    
    {
        
        $this->_select->where('p.primary_role = ?', $this->getCurrentUser()->getPrimaryRoleId());
        return $this->getItems();
    }

    /**
     * @return Zend_Dojo_Data
     * Only own branch user data
     */
    public function getOwnBranchDojoData()    
    {
        $this->_select->where('p.branch_id = ?', $this->getCurrentUser()->getBranchId());
        return $this->getItems();
    }

    /**
     * @return Zend_Dojo_Data
     * All user data
     */
    public function getAllDojoData()
    {
        return $this->getItems();
    }
}

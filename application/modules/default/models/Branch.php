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

/**
 * Branch offices 
 */
class Core_Model_Branch extends Core_Model_Abstract
{
    public $db;
    private $_branchId;
    
    public function __construct($branchId = null)
    {
        $this->db = Zend_Registry::get('db');

        if (is_numeric($branchId)) {
            $this->_branchId = $branchId;
        }
    }
    
    /**
     * @param int $branchId
     * @return fluent interface
     */    
    public function setBranchtId($branchId = null)
    {
        if (is_numeric($branchId)) {
            $this->_branchId = $branchId;
        }
        return $this;
    }

    /**
     * Create a branch office
     */
    public function create($data)
    {
        unset($data['submit']);
        $this->db->insert('branch', $data);
        $branchId = $this->db->lastInsertId();
        return $branchId;
    }

    /**
     * Edit a branch office
     */
    public function edit($data)
    {
        unset($data['submit']);
        $result = $this->db->update('branch', $data, "branch_id = '" . $this->_branchId . "'");
        return $result;
    }

    /**
     * Fetch details of the branch
     */
    public function fetch()
    {
        if (!is_numeric($this->_branchId)) {
            return false;
        }
        $select = $this->db->select();
		$select->from(array('b'=>'branch'), array('*'))
                ->joinLeft(array('u'=>'user'), 
					'b.branch_manager = u.user_id', array('u.email'=>'email as assignedToEmail'))
				->joinLeft(array('pb'=>'branch'), 
					'b.parent_branch_id = pb.branch_id', array('pb.branch_name'=>'branch_name as parentBranchName'))
                ->where('b.branch_id = ?', $this->_branchId);
        $result = $this->db->fetchRow($select);
        return $result;

    }

    /**
     * Fetch details of every branch
     */
    public function fetchAll()
    {
        $select = $this->db->select();
		$select->from(array('b'=>'branch'), array('*'))
                ->joinLeft(array('u'=>'user'), 
					'b.branch_manager = u.user_id', array('u.email'=>'email as assignedToEmail'))
				->joinLeft(array('pb'=>'branch'), 
					'b.parent_branch_id = pb.branch_id', array('pb.branch_name'=>'branch_name as parentBranchName'));
        $result = $this->db->fetchAll($select);
        return $result;
    }


    /**
     * Set the Id of the branch
     */
    public function setId($branchId = nulll)
    {
        if (is_numeric($branchId)) {
            $this->_branchId = $branchId;   
        }
    }


    /** 
     * Deletes a row in the branch table
     */
    public function delete()
    {
        $where = $this->db->quote($this->_branchId, 'INTEGER');
        $this->_previousLeadData = $this->fetch();
        $result = $this->db->delete('branch', "branch_id = $where");
        return $result;
    }


    /**
     * Initiate index search processing object
     * @return Zend_Db_Select object
     */
    public function getListingSelectObject($search, $sort)
    {
        $process = new Core_Model_Branch_Index;
        $processed = $process->getListingSelectObject($search, $sort);
        return $processed;
    }


    /**
     * @return string branch name
     */
    public function getName()
    {
        $branchData = $this->fetch();
        return $branchData->branch_name;
    }
}



<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation,  version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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



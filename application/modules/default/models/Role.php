<?php
class Core_Model_Role extends Core_Model_Abstract
{

    protected $_dbTableClass = 'Core_Model_DbTable_Role';

    /**
     * The role id
     */
    protected $_roleId;



    public function __construct($roleId = null)
    {
        if (is_numeric($roleId)) {
            $this->_roleId = $roleId;
        }
        parent::__construct();
    }

    /**
     * @return array of returned row
     */   
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where('role_id = ?', $this->_roleId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;

    }

    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select();
        $result = $table->fetchAll($select);
        if ($result) {
            $rows = $result->toArray();
            return $rows;
        }
    }

    /**
     * Edit the role information
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('role_id = ?', $this->_roleId);
        $table->update($data, $where);
    }

    /**
     * Delete the role
     */
    public function delete()
    {
        $userRoleModel = new Core_Model_UserRole;
        $result = $userRoleModel->fetchRolesByRoleId($this->_roleId);
        if ($result) {
            return false;
        }
        
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('role_id = ?', $this->_roleId);
        return $table->delete($where);
    }

}

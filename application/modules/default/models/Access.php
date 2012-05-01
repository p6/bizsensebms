<?php
class Core_Model_Access extends Core_Model_Abstract
{
    protected $_dbTableClass = 'Core_Model_DbTable_Access';


    /**
     * @return array|null 
     * @param int $roleId
     */
    public function fetchAllByRoleId($roleId)
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from($table, array('role_id', 'privilege_id'))
               ->join('privilege', 
                    'privilege.privilege_id = access.privilege_id',
                    array('privilege.name as privilege_name'))
                ->join('role',
                    'access.role_id = role.role_id',
                    'role.name as role_name')
                ->where('access.role_id = ?', $roleId);
        $result = $table->fetchAll($select);
        if ($result) {
           $result = $result->toArray(); 
        }
        return $result;    
    }

    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select();
        $rowset = $table->fetchAll();
        if ($rowset) {
            $rowset = $rowset->toArray();
        }
        return $rowset;
    }

}


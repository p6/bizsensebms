<?php
class Core_Model_Privilege extends Core_Model_Abstract
{
    protected $_dbTableClass = 'Core_Model_DbTable_Privilege';
  
    protected $_privilegeId;
    
    public function setPrivilegeId($id)
    {
        $this->_privilegeId = $id;
    }

    public function getIdByName($name)
    {
        $table = $this->getTable();
        $select = $table->select()
                        ->where('name = ?', $name);
        $result = $table->fetchRow($select);
        if ($result) {
            $row = $result->toArray();
            return $row['privilege_id'];
        }
        return $result;

    }

    public function getNameByPrivilegeId($id)
    {
        $table = $this->getTable();
        $select = $table->select()
                        ->where('privilege_id = ?', $id);
        $result = $table->fetchRow($select);
        if ($result) {
            $row = $result->toArray();
            return $row['name'];
        }
        return $result;

    }

    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('privilege_id = ?', $this->_privilegeId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
}

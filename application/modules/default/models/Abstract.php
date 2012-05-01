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
 * an electronic mail 
 * to info@binaryvibes.co.in
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
 * @category    BizSense
 * @package     Core
 * @copyright   Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd.
 * @version    $Id:$
 */

/**
 * Abstract class for models
 *
 * @category    BizSense
 * @package     Core
 * @subpackage  Core_Model
 * @copyright   Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt Ltd
 */
abstract class Core_Model_Abstract
{
    /**
     * @var object Zend_Db_Table objectda tabase table
     */
    protected $_table;

    /**
     * @var string Zend_Db_Table_Abstract class name
     *
     * Table class name
     * Replace ? with the actual class name
     * For example, 'DbTable_User'
     */
    protected $_dbTableClass = 'DbTable_?';

    /**
     * @var array of objects of Observers attached to this object
     */
    protected $_observers = array();

    /**
     * @var array the default observers 
     * The names of the observer classes
     * When the current object is instantiated these observers are attached
     * The array contains class names not objects
     */
    protected $_defaultObservers = array();


    /**
     * @var string Status of the user
     */
    protected $_status;

    /**
     * @var Ephemeral data primarily used by observers
     * Store the values in keys known to the observers
     * For example $_ephemeralData['oldLead'] is the leadData before it was updated
     */
    protected $_ephemeralData = array();


    /**
     * @var object Current user object
     */
    protected $_currentUser;


    /**
     * @var object Core_Service_Log
     */
    protected $_logger;

    /**
     * @var object Core_Service_Log
     */
    protected $_generalLogger;

    /**
     * Set the default db adapter
     * @deprecated 
     * The default db adapter is set only to provide backward compatibility
     * All the models have to operate on Zend_Db_Table objects soon
     * The db adapter should be fetched from 
     * Zend_Db_Table_Abstract::getAdapter method where needed
     */
    public function __construct($id = null)
    {
        $this->attachDefaultObservers();
        $this->setId($id);    
    }
   
    /**
     * Set the id of the entity
     */
    public function setId($id)
    {
    }
    
    /**
     * @return Zend_Db_Table object
     */    
    public function getTable()
    {
        if (null === $this->_table) {
            $class = $this->_dbTableClass;
            $this->_table = new $class;        
        }
        return $this->_table;
    }


    /**
     * Fetch a single recrod from the persistance
     */
    public function fetch()
    {
    }       

    /**
     * Fetch all records from persistance 
     * @return Zend_Db_Table_Rowset object
     */
    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select();
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * Create a record in the db    
     * @return lastInsertId from the db table create operation
     */
    public function create($data)
    {
        $data = $this->unsetNonTableFields($data);
        $table  = $this->getTable();
        return $table->insert($data);
    }

    /**
     * @param array $data contains the db columns as keys and values as values
     * If the data contains other keys unset them
     * @return array $data that can be inserted into the database
     */
    public function unsetNonTableFields($data = array())
    {
        $table  = $this->getTable();
        $fields = $table->info(Zend_Db_Table_Abstract::COLS);
        if (count($data)) {
            foreach ($data as $field => $value) {
                if (!in_array($field, $fields)) {
                    unset($data[$field]);
                }
            }
        }
        return $data;
    }

    /**
     * Edit a rocord in the db
     * @param array $data contains the edited data 
     */
    public function edit($data = array())
    {
    }

    /*
     * Delete a record from the database
     */
    public function delete()
    {
    }

    /**
     * Attach the observer
     * @param object $object to be attached
     */
    public function attach($object)
    {
        $this->_observers[] = $object;
    }

    /*
     * Default observers of the application
     */
    public function attachDefaultObservers()
    {
        $observers = $this->_defaultObservers;
        foreach ($observers as $observer) {
            $object = new $observer;
            $this->attach($object);
        }
    }

    /**
     * Notify all observers
     */
    public function notify()
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
                    
    }

    /**
     * @return status of the object
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return status of the object
     */
    public function setStatus($status = null)
    {
        $this->_status = $status;
        $this->notify();
        return $this;
    }

    /**
     * @return array of ephemeral data
     */
    public function getEphemeral()
    {
        return $this->_ephemeralData;
    } 

    /**
     * Fetch the data before we lose it
     * we lose data when it is updated or deleted
     */
    public function prepareEphemeral()
    {
        $this->_ephemeralData = $this->fetch();
    }

    /**
     * @return @Zend_Pagintor object
     */
    public function getPaginator($search = null, $sort = null)
    {
       $indexClass = get_Class($this) . '_Index';
       $indexObject = new $indexClass($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    }

    /**
     * @return object current user
     */
    public function getCurrentUser()
    {
        return Zend_Registry::get('user');
    }

    /**
     * @return object Zend_Log
     */
    public function getLoggerService()
    {
        if ($this->_logger == null) {
            $this->_logger = new Core_Service_Log;
        }
        return $this->_logger;
    }

    /**
     * @return object Zend_Log
     */
    public function getGeneralLoggerService()
    {
        if ($this->_generalLogger == null) {
            $this->_generalLogger = new Core_Service_Log_General;
        }
        return $this->_generalLogger;
    }

    /**
     * @return object Zend_Cache
     */
    public function getCache()
    {
        return Zend_Registry::get('cache');
    }
}


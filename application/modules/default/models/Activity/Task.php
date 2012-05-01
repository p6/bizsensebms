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

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Activity_Task extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';

    /**
     * Reminder constants
     */
    const TASK_REMINDER_STATUS_NONE = 0;
    const TASK_REMINDER_STATUS_FIVE_MINUTE = 1;
    const TASK_REMINDER_STATUS_FIFTEEN_MINUTE = 2;
    const TASK_REMINDER_STATUS_THIRTY_MINUTE = 3;
    const TASK_REMINDER_STATUS_ONE_HOUR = 4;
    const TASK_REMINDER_STATUS_ONE_DAY = 5;
    const TASK_REMINDER_STATUS_ONE_WEEK = 6;

    /**
     * @var int task ID
     */
    protected $_taskId;
   
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
 
    protected $_dbTableClass = 'Core_Model_DbTable_Activity_Task';

    /**
     * @var array the default observers
     */
    protected $_defaultObservers = array(
        'Core_Model_Activity_Task_Notify_Email'
    );

    /**
     * @param int taskId
     */
    public function __construct($taskId = null)
    {
        if (is_numeric($taskId)) {
            $this->_taskId = $taskId;
        }
        parent::__construct($taskId);
    }

    public function setTaskId($taskId)
    {
        if (is_numeric($taskId)) {
            $this->_taskId = $taskId;
        }
        return $this;
    }

    public function getTaskId()
    {
        return $this->_taskId;
    }

    /**
     * Create a task entry
     * @param array $data
     * @return int task ID
     */
    public function create($data = array())
    {
        $table = $this->getTable();

        $fullStartDate = $data['start_date'].$data['start_time'];
        $startDate = new Zend_Date($fullStartDate);
        $startTimeStamp = $startDate->getTimeStamp();

        $fullEndDate = $data['end_date'].$data['end_time'];
        $endDate = new Zend_Date($fullEndDate);
        $endTimeStamp = $endDate->getTimeStamp();
        unset($data['start_time']);
        unset($data['end_time']);
        $data['start_date'] = $startTimeStamp;
        $data['end_date'] = $endTimeStamp;
        $data['created'] = time();
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        $result = $table->insert($data);
        $this->setTaskId($result);
        $this->setStatus(self::STATUS_CREATE);
        return $result;
    }

    /**
     * @return array the task record
     */
    public function fetch()
    {
        if (!is_numeric($this->_taskId)){
            return false;
        }

        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('task'=>'task'))
                ->where('task_id = ?', $this->_taskId)
                ->join('user', 'task.assigned_to = user.user_id', 'email')
                ->join('task_status', 
        'task.task_status_id = task_status.task_status_id', 'name as status');
        $result =  $table->fetchRow($select);
        if($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @param array $data
     * @return int 
     */
    public function edit($data = null)
    {
        $table = $this->getTable();

        $fullStartDate = $data['start_date'].$data['start_time'];
        $startDate = new Zend_Date($fullStartDate);
        $startTimeStamp = $startDate->getTimeStamp();

        $fullEndDate = $data['end_date'].$data['end_time'];
        $endDate = new Zend_Date($fullEndDate);
        $endTimeStamp = $endDate->getTimeStamp();
        unset($data['start_time']);
        unset($data['end_time']);
        $data['start_date'] = $startTimeStamp;
        $data['end_date'] = $endTimeStamp;
        $data['created_by'] = $this->getCurrentUser()->getUserId();

        $where = $table->getAdapter()->quoteInto('task_id = ?', $this->_taskId);
        $result = $table->update($data, $where);
        $this->setStatus(self::STATUS_EDIT);
        return $result;
    }

    /**
     * @return int the number of records deleted
     */
    public function delete()
    {
        $this->prepareEphemeral();
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('task_id = ?', $this->_taskId);
        $result = $table->delete($where);
        $this->setStatus(self::STATUS_DELETE);
        return $result;
    }

    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select();
        $result = $table->fetchAll($select);
        return $result;
    }

    /**
     * @return object Core_Model_Activity_Task_Notes
     */
    public function getNotes()
    {
        $notes = new Core_Model_Activity_Task_Notes();
        $notes->setModel($this);
        return $notes;
    }

    public function getTasks()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('t' => 'task'),
            array('t'=>'*'))
            ->where('assigned_to = ?', $this->getCurrentUser()->getUserId())
            ->where('t.task_status_id != ?', 3)
            ->join('task_status', 'task_status.task_status_id=t.task_status_id', 'closed_context')
            ->where('closed_context = ?', 0)
            ->order(array('created DESC'))
            ->limit(5, 0);

        $result = $table->fetchAll($select);
        return $result;
    }
}

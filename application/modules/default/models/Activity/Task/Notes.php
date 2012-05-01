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
class Core_Model_Activity_Task_Notes extends Core_Model_Abstract
{
    /**
     * @var the task_notes_id on which we are operating
     */
    protected $_taskNotesId;

    /**
     * @var Zend_Db_Table object 
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Activity_TaskNotes';


    /**
     * @param object task the Core_Model_Activity_Task 
     * @return object Core_Model_Activity_Task_Notes
     */
    public function setModel($task)
    {
        $this->_model = $task;
        return $this;
    }


    /*
     * Inserts a row in the account_notes table
     * @param array $data with keys
     * keys - note, task_id   
     */
    public function create($data = array())
    {
		$data['created'] =  time();
        $data['task_id'] = $this->_model->getTaskId();
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        $result = parent::create($data);
        return $result;
    }

    /**
     * @return array task notes record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('task_notes_id = ?', $this->_taskNotesId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return Zend_Paginator object
     */
    public function getPaginator($sort = null, $search = null)
    {
        $table = $this->getTable();
        $select = $table->select();
        $select->where('task_id = ?', $this->_model->getTaskId());
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }   
}



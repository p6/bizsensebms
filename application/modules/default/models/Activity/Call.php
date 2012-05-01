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
class Core_Model_Activity_Call extends Core_Model_Abstract
{

    const TO_TYPE = 'call to type';
    const TO_TYPE_LEAD = 1;
    const TO_TYPE_CONTACT = 2;

    /**
     * Status constants
     */
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';


    /**
     * @var array the default observers
     */
    protected $_defaultObservers = array(
        'Core_Model_Activity_Call_Notify_Email'
    );

    /**
     * @var int sale stage ID
     */    
    protected $_callId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Activity_Call';



    /**
     * @param int callId
     */
    public function __construct($callId = null)
    {
        if (is_numeric($callId)) {
            $this->_callId = $callId;
        }
        parent::__construct($callId);
    }

    /**
     * @var int return call ID
     */    
    public function setCallId($callId)
    {
        if (is_numeric($callId)) {
            $this->_callId = $callId;
        }
        return $this;
    }

    public function getCallId()
    {
        return $this->_callId;
    }

    /**
     * Create a call entry
     * @param array $data
     * @return int call ID
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
        if ($data['to_type'] == 1) {
            $toType = Core_Model_Activity_Call::TO_TYPE_LEAD;
            $toTypeId = $data['lead_id'];
        } else {
            $toType = Core_Model_Activity_Call::TO_TYPE_CONTACT;
            $toTypeId = $data['contact_id'];
        }
        unset($data['lead_id']);
        unset($data['contact_id']);
        $data['to_type'] = $toType;
        $data['to_type_id'] = $toTypeId;
        $data['created'] = time();
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        $result = $table->insert($data);
        $this->setCallId($result);
        $this->setStatus(self::STATUS_CREATE);
        return $result;
    }

    /**
     * @return array the call record
     */
    public function fetch()
    {
        if (!is_numeric($this->_callId)){
            return false;
        }
        $table = $this->getTable();
        $selectType = $table->select()->setIntegrityCheck(false);
        $selectType->from(array('call'=>'call'))
                    ->where('call_id = ?', $this->_callId)
                    ->join('user', 'user.user_id = call.created_by', 'email as createdby');
        $resultType = $table->fetchRow($selectType);
        if($resultType) {
            $resultType = $resultType->toArray();
            $toType = $resultType['to_type'];
            $createdBy = $resultType['createdby'];
        }
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('call'=>'call'))
                ->where('call_id = ?', $this->_callId)
                ->join('call_status', 'call.call_status_id = call_status.call_status_id', 'name as statusname')
                ->join('user', 'call.assigned_to = user.user_id', 'email');
        if($toType == self::TO_TYPE_LEAD) {
            $select->join('lead', 'call.to_type_id = lead.lead_id', array('first_name', 'middle_name', 'last_name'));
        } elseif($toType == self::TO_TYPE_CONTACT) {
            $select->join('contact', 'call.to_type_id = contact.contact_id', array('first_name', 'middle_name', 'last_name'));
        }
        $result =  $table->fetchRow($select);
        if($result) {
            $result = $result->toArray();
            $result['createdby'] = $createdBy;
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

        if ($data['to_type'] == 1) {
            $toType = Core_Model_Activity_Call::TO_TYPE_LEAD;
            $toTypeId = $data['lead_id'];
        } else {
            $toType = Core_Model_Activity_Call::TO_TYPE_CONTACT;
            $toTypeId = $data['contact_id'];
        }
        unset($data['lead_id']);
        unset($data['contact_id']);
        $data['to_type'] = $toType;
        $data['to_type_id'] = $toTypeId;

        $where = $table->getAdapter()->quoteInto('call_id = ?', $this->_callId);
        $result = $table->update($data, $where);
        $this->setStatus(self::STATUS_EDIT);
        return $result;
    }

    /**
     * @return int the number of records deleted
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('call_id = ?', $this->_callId);
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
     * @return array the number of records fetched related to leads 
     */
    public function fetchLeads($leadId)
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('call'=>'call'))
            ->where('to_type = ?', self::TO_TYPE_LEAD)
            ->join('lead', 'lead.lead_id = call.to_type_id', 
                    array('first_name', 'middle_name', 'last_name'))
            ->where('to_type_id = ?', $leadId);
        $result = $table->fetchAll($select);
        if($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return array the number of records fetched related to leads 
     */
    public function fetchContacts($contactId)
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('call'=>'call'))
            ->where('to_type = ?', self::TO_TYPE_CONTACT)
            ->join('contact', 'contact.contact_id = call.to_type_id', 
                    array('first_name', 'middle_name', 'last_name'))
            ->where('to_type_id = ?', $contactId);
        $result = $table->fetchAll($select);
        if($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @return object Core_Model_Activity_Call_Notes
     */
    public function getNotes()
    {
        $notes = new Core_Model_Activity_Call_Notes();
        $notes->setModel($this);
        return $notes;
    }

    public function sendReminders()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('call'=>'call'))
            ->where('reminder_sent = ?', 0)
            ->join('user', 'call.assigned_to = user.user_id', 'email')
            ->join('profile', 'call.assigned_to = profile.user_id',
                array('first_name', 'middle_name', 'last_name'));
        $result = $table->fetchAll($select);
        return $result;
    }

    public function updateReminderSent($callId)
    {
        $table = $this->getTable();
        $data = array('reminder_sent'=>'1');
        $where = $table->getAdapter()->quoteInto('call_id = ?', $callId);
        $table->update($data, $where);
    }

    public function getCalls()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('c' => 'call'),
            array('c'=>'*'))
            ->where('assigned_to = ?', $this->getCurrentUser()->getUserId())
            ->where('c.call_status_id != ?', 2)
            ->join('call_status', 'call_status.call_status_id=c.call_status_id', 'context')
            ->where('context = ?', 0)
            ->order(array('created DESC'))
            ->limit(5, 0);
        $result = $table->fetchAll($select);
        return $result;        
    }
}

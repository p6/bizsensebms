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
class Core_Model_Opportunity extends Core_Model_Abstract
{
    const STATUS_DELETE = 'DELETE';
    const STATUS_CREATE = 'CREATE';
    const STATUS_EDIT = 'EDIT';


    /**
     * @const the opportunity customer type
     */
    const CUSTOMER_TYPE_ACCOUNT = 1; 
    const CUSTOMER_TYPE_CONTACT = 2;


    /**
     * @deprecated
     */
    public $db;

    protected $_opportunity;

    /**
     *@deprecated. Use _opportunityId instead
    
    protected $_opportunity_id;
     */
     
    /**
     * @var the opportunity ID
     */
    protected $_opportunityId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Opportunity';


    /**
     * @param opportunityId
     */
    public function __construct($opportunityId = null)
    {
        $this->db = Zend_Registry::get('db');
        if (is_numeric($opportunityId)) {
            $this->_opportunityId = $opportunityId;
        }

    }


    /**
     * @param int $id the opportunity ID
     * @return object Core_Model_Opportunity
     */
    public function setOpportunityId($id)
    {
        $this->_opportunityId = $id;
        return $this;
    }

    /**
     * @return int the opportunity ID
     */
    public function getOpportunityId()
    {
        return $this->_opportunityId;
    }

    /** 
     * Sets the status upon CRUD actions
     * @param CONST STATUS_?
     * Calls Opportunity_Notify_Email::update
     */
    public function setStatus($status = null)
    {
        $this->_status = $status;
        $this->_notify();
    }

    /**
     * Get the status of the opportunity
     */
    public function getStatus()
    {
        return $this->_status;
    }


    /**
     * Notifies the observers
     */
    protected function _notify()
    {
        $status = $this->_status;
        $emailNotify = new Core_Model_Opportunity_Notify_Email();
        $emailNotify->update($this);
    }

    /*
     * Inserts a record in the opportunity table
     * @param $opportunityData form input
     */
    public function create($opportunityData = array())
    {
        $opportunityData['created'] = time();
        $date = new Zend_Date($opportunityData['expected_close_date']);
        $opportunityData['expected_close_date'] = $date->getTimestamp();
        if (!is_numeric($opportunityData['campaign_id'])) {
            $opportunityData['campaign_id'] = null;
        }
        $table = $this->getTable();
        $opportunityId = $table->insert($opportunityData);
        $this->_opportunityId = $opportunityId;
        $this->setStatus(self::STATUS_CREATE);
        
        $log = $this->getLoggerService();
        $info = 'Opportunity created with lead id = '. $this->_opportunityId ;
        $log->info($info);
        
        return $opportunityId; 
    }


    /**
     * Fetches a single opportunity record from db 
     * Based on currently set opportunityId
     */
    public function fetch()
    {
        $db = $this->db;

        $select = $db->select();
        $select->from(array('o' => 'opportunity'),
                  array('o'=>'*'))
            ->joinLeft(array('ls'=>'lead_source'),
                    'o.lead_source_id = ls.lead_source_id', 
                        array('ls.name'=>'name as source'))
            ->joinLeft(array('ss'=>'sales_stage'),
                    'o.sales_stage_id = ss.sales_stage_id', 
                    array('ss.name'=>'name as stage'))
            ->joinLeft(array('a'=>'account'),
                    'o.account_id = a.account_id', 
                    array('a.account_name as account_name'))
            ->joinLeft(array('u'=>'user'), 
                        'u.user_id = o.assigned_to', 
                        array('u.email'))
            ->joinLeft(array('b'=>'branch'), 
                        'b.branch_id = o.branch_id', 
                        array('b.branch_name'))
            ->joinLeft(array('c'=>'contact'), 
                    'c.contact_id = o.contact_id', 
                    array('c.first_name as firstName', 
                        'c.middle_name as middleName', 
                        'c.last_name as lastName'))    
            ->where('opportunity_id = ?', $this->_opportunityId);
        $result = $db->fetchRow($select);
        return $result;

    } 

    /**
     * Fetches all opportunity records from db 
     */
    public function fetchAll()
    {
        $db = $this->db;

        $select = $db->select();
        $select->from(array('o' => 'opportunity'),
                  array('o'=>'*'))
            ->joinLeft(array('ls'=>'leadSource'),
                'o.leadSourceId = ls.leadSourceId', 
                    array('ls.name'=>'name as source'))
            ->joinLeft(array('ss'=>'salesStage'),
                'o.salesStageId = ss.salesStageId', 
                    array('ss.name'=>'name as stage'))
            ->joinLeft(array('a'=>'account'),
                    'o.accountId = a.accountId', 
                    array('a.accountName as accountName'))
            ->joinLeft(array('u'=>'user'), 'u.uid = o.assignedTo', 
                    array('u.email'))
            ->joinLeft(array('b'=>'branch'), 'b.branchId = o.branchId', 
                array('b.branchName'))
            ->joinLeft(array('c'=>'contact'), 'c.contactId = o.contactId', 
                    array('c.firstName as firstName', 
                        'c.middleName as middleName', 
                        'c.lastName as lastName'));    
        $result = $db->fetchRow($select);
        return $result;

    } 

    /**
     * Builds the Zend_Db_Select object 
     */
    public function getfetchAllSelectObject()
    {
        $db = $this->db;

        $select = $db->select();
        $select->from(array('o' => 'opportunity'),
                  array('o'=>'*'))
            ->joinLeft(array('ls'=>'lead_source'),
                'o.lead_source_id = ls.lead_source_id', 
                array('ls.name'=>'name as source'))
            ->joinLeft(array('ss'=>'sales_stage'),
                'o.sales_stage_id = ss.sales_stage_id', 
                array('ss.name'=>'name as stage'))
            ->joinLeft(array('a'=>'account'),
                    'o.account_id = a.account_id', 
                    array('a.account_name as account_name'))
            ->joinLeft(array('u'=>'user'), 
                        'u.user_id = o.assigned_to', 
                        array('u.email'))
            ->joinLeft(array('b'=>'branch'), 
                    'b.branch_id = o.branch_id', 
                    array('b.branch_name'))
            ->joinLeft(array('c'=>'contact'), 'c.contact_id = o.contact_id', 
                    array('c.first_name as firstName', 
                        'c.middle_name as middleName', 
                        'c.last_name as lastName'));    
        return $select;

    } 

    /**
     * Updates the row in the opportunity table
     */
    public function edit($data = array())
    {
        $opportunityId = $this->_opportunityId;
        $data['updated'] = time();
        if (!is_numeric($data['campaign_id'])) {
            $data['campaign_id'] = null;
        }
        $date = new Zend_Date($data['expected_close_date']);
        $data['expected_close_date'] = $date->getTimestamp();
        $table = $this->getTable();
        $data = $this->unsetNonTableFields($data);
        $where = $table->getAdapter()->quoteInto('opportunity_id = ?', $opportunityId);
        $this->prepareEphemeral();
        $result = $table->update($data, $where);
        $this->setStatus(self::STATUS_EDIT);
        
        $log = $this->getLoggerService();
        $info = 'Opportunity edited with lead id = '. $this->_opportunityId ;
        $log->info($info);
        
        return $resut;
    }


    /**
     * Delete an opportunity
     * @return int number of opportunities deleted
     */
    public function delete()
    {
        $where = $this->db->quote($this->_opportunityId, 'INTEGER');     
        $result = $this->db->delete('opportunity', "opportunity_id = $where");
        $this->setStatus(self::STATUS_DELETE);
        
        $log = $this->getLoggerService();
        $info = 'Opportunity deleted with lead id = '. $this->_opportunityId ;
        $log->info($info);
        
        return $result;
    }

    /**
     * @deprecated
     */
    public function filterInput($data)
    {
        if (!is_numeric($data['contact_id'])) {
            $data['contact_id'] = null;
        }
        unset($data['submit']);
        return $data; 
    }

    /**
     * Initiate index search processing object
     * @return Zend_Db_Select object
     */
    public function getListingSelectObject($search, $sort)
    {
        $process = new Core_Model_Opportunity_Index;
        $processed = $process->getListingSelectObject($search, $sort);
        return $processed;    
    }

    /**
     * Generate Dojo data to assign opportunity to user
     */
    public function getAssignedToDojoData()
    {
        $opportunityData = new Core_Model_Opportunity_Data; 
        $dojoData = $opportunityData->getAssignedToDojoData();   
        return $dojoData;
    }

    /**
     * Generate Dojo data to assign opportunity to branch
     */
    public function getAssignedToBranchDojoData()
    {
        $opportunityData = new Core_Model_Opportunity_Data; 
        $dojoData = $opportunityData->getAssignedToBranchDojoData();   
        return $dojoData;
    }

    /**
     * @return object Core_Model_Opportunity_Notes
     */
    public function getNotes()
    {
        $notes = new Core_Model_Opportunity_Notes();
        $notes->setModel($this);
        return $notes;
    }

    public function accountOpportunity($accountId)
    {
        $table = $this->getTable();        
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('account_id = ?', $accountId);
        $result = $table->fetchAll($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }

    public function contactOpportunity($contactId)
    {
        $table = $this->getTable();        
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('contact_id = ?', $contactId);
        $result = $table->fetchAll($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }

    /**
     * @param int campaignId
     * @return array the opportunities record with campaignId
     */
    public function getOpportunitiesByCampaignId($campaignId)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('campaign_id = ?', $campaignId);

        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
 
    }

    /**
     * Fetch contacts for dashlets
     */
    public function getOpportunities()
    {
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('o' => 'opportunity'),
            array('o'=>'*'))
            ->where('assigned_to = ?', $this->getCurrentUser()->getUserId())
            ->join('sales_stage', 'sales_stage.sales_stage_id=o.sales_stage_id', 'context')
            ->where('context = ?', 0)
            ->order(array('created DESC'))
            ->limit(5, 0);

        $result = $table->fetchAll($select);
        return $result;
    }

}


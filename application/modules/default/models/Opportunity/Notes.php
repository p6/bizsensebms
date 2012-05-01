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
class Core_Model_Opportunity_Notes extends Core_Model_Abstract
{
    const STATUS_CREATED = 'Opportunity notes created';
    /**
     * @var the opportunity_notes_id on which we are operating
     */
    protected $_opportunityNotesId;

    /**
     * @var object the opportunity model
     */
    protected $_model;

    /**
     * @var Zend_Db_Table object 
     */
    protected $_dbTableClass = 'Core_Model_DbTable_OpportunityNotes';

    protected $_defaultObservers = array(
        'Core_Model_Opportunity_Notes_Notify_Email'
    );

    /**
     * @param object opportunity the Core_Model_Opportunity 
     * @return object Core_Model_Opportunity_Notes
     */
    public function setModel($opportunity)
    {
        $this->_model = $opportunity;
        return $this;
    }
   
    /**
     * @return object the opportunity model
     */
    public function getModel()
    {
       return $this->_model; 
    }

    /**
     * @return int Opportunity notes ID
     */
    public function getOpportunityNotesId()
    {
        return $this->_opportunityNotesId;
    }

    /**
     * @param int leadNotesId
     * @return fluent interface
     */

    public function setOpportunityNotesId($opportunityNotesId)
    {
       $this->_opportunityNotesId = $opportunityNotesId; 
       return $this;
    }

    /*
     * Inserts a row in the account_notes table
     * @param array $data with keys
     * keys - note, opportunity_id   
     */
    public function create($data = array())
    {
		$data['created'] =  time();
        $data['opportunity_id'] = $this->_model->getOpportunityId();
        $data['created_by'] = Core_Model_User_Current::getId();

        $result = parent::create($data);
        $this->_opportunityNotesId = $result;
        $this->setStatus(self::STATUS_CREATED);
        return $result;
    }

    /**
     * @return Zend_Paginator object
     */
    public function getPaginator($sort = null, $search = null)
    {
        $table = $this->getTable();
        $select = $table->select();
        $opportunityId = $this->_model->getOpportunityId();
        $select->where('opportunity_id = ?', $this->_model->getOpportunityId());
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }   
    
    /*
     * Fetches a single record in the account table
     * @return result object from Zend_Db_Select
     * based on the opportunityId
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('opportunity_notes_id = ?', $this->_opportunityNotesId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

}



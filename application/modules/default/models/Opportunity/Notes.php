<?php
/*
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



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

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Lead_Notes extends Core_Model_Abstract
{
    
    const STATUS_CREATED = 'lead notes created';

    /**
     * @var the lead_notes_id on which we are operating
     */
    protected $_leadNotesId;

    /**
     * @var object the lead model
     */
    protected $_model;

    /**
     * @var Zend_Db_Table object 
     */
    protected $_dbTableClass = 'Core_Model_DbTable_LeadNotes';

    protected $_defaultObservers = array(
        'Core_Model_Lead_Notes_Notify_Email'
    );

    /**
     * @param object lead the Core_Model_Lead 
     * @return object Core_Model_Lead_Notes
     */
    public function setModel($lead)
    {
        $this->_model = $lead;
        return $this;
    }
   
    /**
     * @return object the lead model
     */
    public function getModel()
    {
       return $this->_model; 
    }

    /**
     * @return int lead notes ID
     */
    public function getLeadNotesId()
    {
        return $this->_leadNotesId;
    }

    /**
     * @param int leadNotesId
     * @return fluent interface
     */
    public function setLeadNotesId($leadNotesId)
    {
       $this->_leadNotesId = $leadNotesId; 
       return $this;
    }

    /*
     * Inserts a row in the account_notes table
     * @param array $data with keys
     * keys - note, lead_id   
     */
    public function create($data = array())
    {
		$data['created'] =  time();
        $data['lead_id'] = $this->_model->getLeadId();
        $data['created_by'] = Core_Model_User_Current::getId();
               
        $result = parent::create($data);
        $this->_leadNotesId = $result;
        $this->setStatus(self::STATUS_CREATED);
        return $result;
    }

    /**
     * @return array lead notes record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('lead_notes_id = ?', $this->_leadNotesId);
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
        $leadId = $this->_model->getLeadId();
        $select->where('lead_id = ?', $this->_model->getLeadId());
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }   

}



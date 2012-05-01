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

class Core_Model_Contact_Notes extends Core_Model_Abstract
{
    const STATUS_CREATED = 'contact notes created';
    /**
     * @var the contact_notes_id on which we are operating
     */
    protected $_contactNotesId;

    /**
     * @var object the contact model
     */
    protected $_model;

    /**
     * @var Zend_Db_Table object 
     */
    protected $_dbTableClass = 'Core_Model_DbTable_ContactNotes';

    protected $_defaultObservers = array(
        'Core_Model_Contact_Notes_Notify_Email'
    );
    /**
     * @param object contact the Core_Model_Contact 
     * @return object Core_Model_Contact_Notes
     */
    public function setModel($contact)
    {
        $this->_model = $contact;
        return $this;
    }
   
    /**
     * @return object the contact model
     */
    public function getModel()
    {
       return $this->_model; 
    }

    public function getContactNotesId()
    {
        return $this->_contactNotesId;
    }

    /**
     * @param int leadNotesId
     * @return fluent interface
     */
    public function setContactNotesId($contactNotesId)
    {
       $this->_contactNotesId = $contactNotesId; 
       return $this;
    }


    /*
     * Inserts a row in the account_notes table
     * @param array $data with keys
     * keys - note, contact_id   
     */
    public function create($data = array())
    {
        
		$data['created'] =  time();
        $data['contact_id'] = $this->_model->getContactId();
        $data['created_by'] = Core_Model_User_Current::getId();
         
        $result = parent::create($data);
        $this->_contactNotesId = $result;
        $this->setStatus(self::STATUS_CREATED);
        return $result;
    }
    
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('contact_notes_id = ?', $this->_contactNotesId);
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
        $contactId = $this->_model->getContactId();
        $select->where('contact_id = ?', $this->_model->getContactId());
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }   

}



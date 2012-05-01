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



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
class Core_Model_Account_Notes extends Core_Model_Abstract
{
    const STATUS_CREATED = 'account notes created';
    /**
     * @property the account_id on which we are operating
     */
    protected $_account_id;

    /**
     * @property the account_notes_id on which we are operating
     */
    protected $_accountNotesId;

    /**
     * @property 
     */
    protected $_dbTableClass = 'Core_Model_DbTable_AccountNotes';

    protected $_defaultObservers = array(
        'Core_Model_Account_Notes_Notify_Email'
    );

    /**
     * @var object Core_Model_Account
     */
    protected $_model;
        
    public function setAccountId($account_id)
    {
        $this->_account_id = $account_id;
    }

    /**
     * @return object Core_Model_Account
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param object $account Core_Model_Account
     * @return fluent interface
     */
    public function setModel($account)
    {
        $this->_model = $account;
        return $this;
    }

    /**
     * @return int account notes ID
     */
    public function getAccountNotesId()
    {
        return $this->_accountNotesId;
    }

    /**
     * @param int account_notes_id
     * @return fluent interface
     */
    public function setAccountNotesId($accountNotesId)
    {
       $this->_accountNotesId = $accountNotesId; 
       return $this;
    }
    /*
     * Inserts a row in the account_notes table
     * @param array $data with keys
     * keys - note, account_id   
     */
    public function create($data = array())
    {
		$data['created'] =  time();
        $data['account_id'] = $this->getModel()->getAccountId();
        $data['created_by'] = $this->getCurrentUser()->getUserId();

        $result = parent::create($data);
        $this->_accountNotesId = $result;
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
        $select->where('account_id = ?', $this->_account_id);
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;

    }   

    /*
     * Fetches a single record in the account table
     * @return result object from Zend_Db_Select
     * based on the accountId
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('account_notes_id = ?', $this->_accountNotesId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

}



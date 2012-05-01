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



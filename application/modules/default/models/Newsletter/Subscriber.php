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

class Core_Model_Newsletter_Subscriber extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const STATUS_CREATE = 'CREATE';

    const FORMAT_HTML = 1;
    const FORMAT_TEXT = 0;

    const MESSAGE_HTML = 'HTML';
    const MESSAGE_TEXT = 'TEXT';
    
    const CONFIRMED = 1;
    const UNCONFIRMED = 2;
    const ACTIVE = 3;
    const BLOCKED = 4;

    /**
     * The service subscriber id
     */
    protected $_subscriberId;

    /**
     * @var list model
     */
    protected $_model;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Subscriber';

    /*
     * @param int $subscriberId the ticket ID
     * @return fluent interface    
     */
    public function setSubscriberId($subscriberId)
    {
        if (is_numeric($subscriberId)) {
            $this->_subscriberId = $subscriberId;
        }
        return $this;
    }
   
    /**
     * @return array the subscriber record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('subscriber_id = ?', $this->_subscriberId);
        $result = $table->fetchRow($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
 
    /*
     * Inserts a record in the list_subscriber table
     * @param $subscriberData form input
     */
    public function create($data = array())
    {
        $matches = array();
        preg_match('/([^@]+)$/', $data['email'], $matches);
        $data['domain'] = $matches[1];
        $table = $this->getTable();       
        $this->_subscriberId = $table->insert($data);    
        return $this->_subscriberId;
    }
    
    /**
     * Updates the row in the list_subscriber table
     * @param array $data
     * @return int subscriber ID
     */ 
    public function edit($data = array()) 
    {
        $table = $this->getTable();    
        $where = $table->getAdapter()->quoteInto('subscriber_id = ?', $this->_subscriberId);
        $result = $table->update($data, $where);
        return $result;
    }    

    /** 
     * Delete a row in the subscriber table
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('subscriber_id = ?', $this->_subscriberId);
        $result = $table->delete($where);
        return $result;
    }

    /**
     * @param object $model Core_Model_Newsletter_List
     * @return fluent interface
     */
    public function setModel($listModel)
    {
       $this->_model = $listModel;
       return $this;
    }

    /**
     * @return object Core_Model_Newsletter_List
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = new Core_Model_Newsletter_List;
        }
        return $this->_model;
    }

    public function viewSubscribers($listId)
    {
        $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $listSubscriberIds = $listSubscriberModel->getSubscribersByListId($listId);
        $result = array();
        for ($i = 0; $i < count($listSubscriberIds); $i++ ) {
            $result[] = $this->setSubscriberId(
                            $listSubscriberIds[$i]['subscriber_id'])->fetch();
        }
        return $result;
    }

    /**
     * Import list subscribers from a CSV file
     *
     * @param array $metaData lead meta data like source, status ,etc
     * @param string $location the location of the CSV file.
     *
     * @return void
     */
    public function import($location, $listId)
    {
        $form = new Core_Form_Newsletter_Subscriber_Create;
        $listFrom = new Core_Form_Newsletter_List_Subscriber_Create($listId);
        $noOfAffectedRows = 0;
        $success = null;
        $handle = fopen($location, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $dataToImport = array();
            $subscriberId = null;
            $dataToImport = array(
                'first_name'        =>  $data[0],
                'middle_name'       =>  $data[1],
                'last_name'         =>  $data[2],
                'email'             =>  $data[3],
                'format'            =>  $data[4],
                'status'            =>  self::ACTIVE
            );
            
            $result = $this->fetchAllByEmail($dataToImport['email']);
            
            if ($result) {
                $subscriberId = $result['subscriber_id'];
            }
            else {
                if ($form->isValid($dataToImport)) {
                    $subscriberId = $this->create($dataToImport);
                }
            }
            if($subscriberId) {
                $data = array(
                            'subscriber_id'=>$subscriberId,
                            'list_id' => $listId
                        );
                if($listFrom->isValid($data)) {
                    $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
                    $success = $listSubscriberModel->create($listId, $subscriberId);
                    ++$noOfAffectedRows;
                }
            }
            
        }
        fclose($handle);
        if ($success) {
            return $noOfAffectedRows;
        }
    }

    /**
     * @param int $listId
     * @return array list subscriber collection
     */
    public function fetchAllByListId($listId)
    {
        $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $listSubscriberIds = $listSubscriberModel->getSubscribersByListId($listId);
        $result = array();
        for ($i = 0; $i < count($listSubscriberIds); $i++ ) {
            $result[] = $this->setSubscriberId(
                            $listSubscriberIds[$i]['subscriber_id'])->fetch();
        }
        return $result;
    }
    
    /**
     * @param int $email
     * @return array 
     */
    public function fetchAllByEmail($email)
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('email = ?', $email);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @param int $domain
     * @return array domain subscriber collection
     */
    public function fetchAllByDomain($domain)
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('domain = ?', $domain);
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }
    /**
     * @param int $listId
     * @param string $email
     * @return array record of list subscriber
     */
    public function fetchByListIdAndEmail($listId, $email)
    {
        $listSubscriberDetails = $this->viewSubscribers($listId);
        $result = array();
        for ($i = 0; $i < count($listSubscriberDetails); $i++ ) {
             if (in_array($email,$listSubscriberDetails[$i])) {
                $result[] = $listSubscriberDetails[$i];
             }
        }
        return $result;
    }

    /**
     * @param int $first name
     * @param string $email
     * @return array record of list subscriber
     */
    public function fetchByFirstNameAndEmail($firstName, $email)
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->where('first_name = ?', $firstName)
                        ->where('email = ?', $email);
        $result = $table->fetchAll($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
    
    /**
     * @param string $hash
     * @return bool
     */
    public function unsubscribe($hash)
    {
        $queueModel = new Core_Model_Newsletter_Message_Queue();
        $queueRecord = $queueModel->fetchByHash($hash);
        if ($queueRecord == null) {
            return false;
        }
        $listId = $queueRecord['list_id'];
        $email = $queueRecord['email'];
        $subscriberDetails = $this->fetchAllByEmail($email);
        $subscriberId = $subscriberDetails['subscriber_id'];
        $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $result = $listSubscriberModel->delete($listId, $subscriberId);
        return $result;
    }

    /**
     * @param int $listId
     * @param string $email
     * @param array $data
     * @return int the number of records edited
     */
    public function editByListIdAndEmail($listId, $email, $data)
    {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $subscriberDetails = $this->fetchAllByEmail($email);
        $subscriberId = $subscriberDetails['subscriber_id'];
        $where .= $db->quoteInto('subscriber_id = ?', $subscriberId);
        $result = $table->update($data, $where);
        return $result;
    }

    /**
     * @TODO issue#490
     */
    public function removeByEmailAndListId($email, $listId)
    {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $subscriberDetails = $this->fetchAllByEmail($email);
        $subscriberId = $subscriberDetails['subscriber_id'];
        $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $result = $listSubscriberModel->delete($listId, $subscriberId);
        return $result;
    }
    
    /**
     * @param int $email
     * @param string $listId
     * @return int the number of records edited
     */
    public function blockByEmailAndListId($email, $listId)
    {
        $subscriberDetails = $this->fetchAllByEmail($email);
        $listSubscriberModel = new Core_Model_Newsletter_List_Subscriber;
        $data['status'] = self::BLOCKED;
        $data['blocked_timestamp'] = time();
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('subscriber_id = ?', $subscriberDetails['subscriber_id']);
        $result = $table->update($data, $where);
        return $result;
    }
    
    /**
     * @return @Zend_Pagintor object
    
    public function getPaginator($search = null, $sort = null)
    {
       $indexObject = new Core_Model_Newsletter_List_Subscriber_Index($search, $sort);
       $indexObject->setModel($this);
       return $indexObject->getPaginator();
    } */
    
    /**
     * @return array distinct domain
     */
    public function getDistinctDomain()
    {
        $table = $this->getTable();
        $select = $table->select()
                       ->setIntegrityCheck(false);
        $select->from($table ,array('domain'))
                ->distinct('domain');
        $result = $table->fetchAll($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
    
}

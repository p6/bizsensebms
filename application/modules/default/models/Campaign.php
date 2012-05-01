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
class Core_Model_Campaign extends Core_Model_Abstract
{
    /**
     * Status constants
     */
    const STATUS_CREATE = 'CREATE';

    /**
     * The service item id
     */
    protected $_campaignId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Campaign';

    /*
     * @param int $campaignId the ticket ID
     * @return fluent interface    
     */
    public function setCampaignId($campaignId)
    {
        if (is_numeric($campaignId)) {
            $this->_campaignId = $campaignId;
        }
        return $this;
    }
   
    /**
     * @return array the campaign record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->join('branch',
                                'branch.branch_id = 
                                    campaign.branch_id',
                                array('branch.branch_name as branch_name')
                            )
                        ->join('user',
                                'user.user_id = 
                                    campaign.assigned_to',
                                array('user.email as assigned_name')
                            )
                        ->where('campaign_id = ?', $this->_campaignId);
        $result = $table->fetchRow($select);
        if ($result) {
            return $result->toArray();
        }
        return $result;
    }
 
    /*
     * Inserts a record in the campaign table
     * @param $campaignData form input
     */
    public function create($data = array())
    {
        $data['created'] = time();
        $table = $this->getTable();

        $startDate = $data['start_date'];
        $campaignStartDate = new Zend_Date($startDate);
        $data['start_date'] = $campaignStartDate->getTimestamp();

        $endDate = $data['end_date'];
        $campaignEndData = new Zend_Date($endDate);
        $data['end_date'] = $campaignEndData->getTimestamp();
        $data['created_by'] = $this->getCurrentUser()->getUserId();
        $campaignId = $table->insert($data);
        $this->_campaignId = $campaignId;
        $this->setStatus(self::STATUS_CREATE);
        return $campaignId;
    }

    /**
     * Updates the row in the Campaign table
     * @param array $data
     * @return int campaign ID
     */ 
    public function edit($data = array()) 
    {
        $table = $this->getTable();
        $date = new Zend_Date($data['start_date']);
        $data['start_date'] = $date->getTimestamp();
        $date = new Zend_Date($data['end_date']);
        $data['end_date'] = $date->getTimestamp();

        $where = $table->getAdapter()->quoteInto('campaign_id = ?', $this->_campaignId);
        $result = $table->update($data, $where);
        return $result;
    }    

    /** 
     * Deletes a row in the campaign table
     */
    public function delete()
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('campaign_id = ?', $this->_campaignId);
        $result = $table->delete($where);
        return $result;
    }

    /**
     * @param int campaignId
     * @return int number of leads
     */
     public function getTotalNumberOfLeadsByCampaignId($campaignId)
     {
        $leadModel = new Core_Model_Lead;
        $result = $leadModel->getLeadsByCampaignId($campaignId);
        $totalLeads = count($result);
        return $totalLeads;
     }
    
    /**
     * @param int campaignId
     * @return int number of opportunities
     */    
    public function getTotalNumberOfOpportunitiesByCampaignId($campaignId)
    {
        $opportunityModel = new Core_Model_Opportunity;
        $result = $opportunityModel->getOpportunitiesByCampaignId($campaignId);
        $totalopportunities = count($result);
        return $totalopportunities;
    }

    /**
     * @param int campaignId
     * @return int number of contacts
     */
    public function getTotalNumberOfContactsByCampaignId($campaignId)
    {
        $contactModel = new Core_Model_Contact;
        $result = $contactModel->getContactsByCampaignId($campaignId);
        $totalContacts = count($result);
        return $totalContacts;        
    }

    /**
     * @param int campaignId
     * @return int number of Accounts
     */
    public function getTotalNumberOfAccountsByCampaignId($campaignId)
    {
        $accountModel = new Core_Model_Account;
        $result = $accountModel->getAccountsByCampaignId($campaignId);
        $totalAccounts = count($result);
        return $totalAccounts;        
    }

    /**
     * @param int campaignId
     * @return int number of invoices
     */
    public function getTotalNumberOfInvoicesByCampaignId($campaignId)
    {
        $invoiceModel = new Core_Model_Invoice;
        $result = $invoiceModel->getInvoicesByCampaignId($campaignId);
        $totalInvoices = count($result);
        return $totalInvoices;        
    }

    /**
     * @param int campaignId
     * @return int number of quotes
     */
    public function getTotalNumberOfQuotesByCampaignId($campaignId)
    {
        $quoteModel = new Core_Model_Quote;
        $result = $quoteModel->getQuotesByCampaignId($campaignId);
        $totalQuotes = count($result);
        return $totalQuotes;        
    }

    /**
     * @param int campaignId
     * @return int number of messages
     */
    public function getTotalNumberOfMessagesByCampaignId($campaignId)
    {
        $newsletterMessageModel = new Core_Model_Newsletter_Message;
        $result = $newsletterMessageModel->getMessagesByCampaignId($campaignId);
        $totalQuotes = count($result);
        return $totalQuotes;        
    }

}

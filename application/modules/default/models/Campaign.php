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

/**
 * Campain 
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

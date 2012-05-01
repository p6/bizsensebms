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
class Core_Service_WebService_Rest_Lead
{
    /**
     * @var object Core_Model_Contact
     */
    protected $_leadModel;
    
    /**
     * @var lead Id
     */
    protected $_leadId;
    
    /**
     * @return object Core_Model_Contact
     */
    public function getLeadModel()
    {
        if (!$this->_leadModel) {
            $this->_leadModel = new Core_Model_Lead;
        }
        return $this->_leadModel;
    }
    
    /**
     * Set the lead ID
     * @param int $leadId
     */
    public function setLeadId($leadId)
    {
        if (!is_numeric($leadId)) {  
            throw new Exception('Lead ID must be an integer');
        }
        $this->_leadId = $leadId;
        return $this;   
    }
    
    /**
     * Get the default lead assignee
     * @param int user id
     */
    public function getDefaultAssigneeId()
    {
        $variable = new Core_Model_Variable(self::VARIABLE_KEY_DEFAULT_ASSIGNEE_ID);
        return $variable->getValue();
    }
    
    /**
     * @return array the contact record
     */
    public function fetch()
    {
       return $this->getLeadModel()->setLeadId($this->_leadId)->fetch();
    }
    
    /**
     * Creates a row in the lead table
     * @param array $data to be stored
     */
    public function create($data = array())
    {
        return $this->getLeadModel()->create($data);
    }
}

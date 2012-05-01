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

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

class Core_Form_Opportunity_Edit extends Core_Form_Opportunity_Create
{
    /**
     * @var the opportunity ID
     */
    protected $_opportunityId;

    function __construct($opportunityId)
    {
        $this->_opportunityId = $opportunityId;
        parent::__construct();
    }
    
    public function init()
    {

        parent::init();

        $this->getElement('submit')->setLabel('Edit Opportunity');
	
	$this->removeElement('no_csrf_opportunity_create');

	$this->addElement('hash', 'no_csrf_opportunity_edit',
            array(
                'salt' => 'unique',
                'ignore' => true,
            )
        );
  
        $opportunity = new Core_Model_Opportunity($this->_opportunityId);
        $row = $opportunity->fetch();
        Zend_Date::setOptions(array('format_type' => 'php'));
        $expectedCloseDateTimestamp = new Zend_Date($row->expected_close_date);
        $expectedCloseDate = $expectedCloseDateTimestamp->toString('Y-m-d');
        $row->expected_close_date = $expectedCloseDate;
        $casted = (array)$row;
        $this->populate($casted);


    }
 
 
}

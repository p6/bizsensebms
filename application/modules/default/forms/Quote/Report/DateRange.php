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

Class Core_Form_Quote_Report_DateRange extends Zend_Form
{
    public function init()
    {
        $this->setMethod('get');
        $this->setName('search');
        
        $from = new Zend_Dojo_Form_Element_DateTextBox('from');
        $from->setLabel('From');

        $to = new Zend_Dojo_Form_Element_DateTextBox('to');
        $to->setLabel('To');
        
        $accountId = new Core_Form_Account_Element_Account;
        $accountElement = $accountId->getElement();
        $accountElement->setStoreParams(array('url'=>'/account/jsonstore'));
        $this->addElement($accountElement); 

        $contactId = new Core_Form_Contact_Element_Contact;
        $contactElement = $contactId->getElement();
        $this->addElement($contactElement);
        
        $assignedToContainer = new Core_Form_User_Element_AssignedTo;
        $assignedToContainer->setLabel('Assign Lead To');
        $assignedToContainer->setName('assigned_to');
        $assignedTo = $assignedToContainer->getElement();
        $this->addElements(array($assignedTo));
        
        $status = new Core_Form_Quote_Element_QuoteStatus;
        $statusElement = $status->getElement();
        $statusElement->setStoreParams(array('url'=>'/quotestatus/jsonstore'));
        $this->addElement($statusElement);
        
        $submit = $this->createElement('submit', 'submit');
        $submit->setAttrib('class', 'submit_button');
        $submit->setLabel('Search');
        
        $this->addElements(
            array(
                $from, $to, $submit
            )
        );

    }
}


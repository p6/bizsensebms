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

class Core_Form_Ticket_Create extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'title', array
            (
                'label' => 'Title',
                'required' => true,
                'validators' => 
                     array(
                       'validator' =>  (new Zend_Validate_StringLength(0, 250))
                     )
            )
        ); 

        $this->addElement('textarea', 'description', array
            (
                'label' => 'Description',
                'required' => true,
                'attribs' => array(
                    'rows' => 10,
                    'cols' => 60,
                )
            )
        ); 

        $element = new Core_Form_Contact_Element_Contact;
        $contact = $element->getElement();
        $contact->setRequired(true);
        $this->addElement($contact);

        $status = $this->createElement('select', 'ticket_status_id');
        $status->setLabel('Status')
                ->setRequired(true);
        $ticketStatusModel = new Core_Model_Ticket_Status();
        $ticketStatusCollection = $ticketStatusModel->fetchAll();
        foreach ($ticketStatusCollection as $item) {
           $status->addMultiOption($item['ticket_status_id'], $item['name']); 
        }
        $this->addElement($status);

        $this->addElement('submit', 'submit', array
            (
                'ignore' => true,
                'class' => 'submit_button'
            )
        );

    }
}

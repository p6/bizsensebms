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

class Core_Form_Org_Edit extends Zend_Form
{
    
    public function init()
    {
        $db = Zend_Registry::get('db');

        $org = $db->fetchRow("SELECT * FROM organization_details", null, Zend_Db::FETCH_ASSOC);

        $this->setAction('/admin/org/edit');
        $this->setMethod('post');


        $companyName = $this->createElement('text', 'company_name')
                            ->setLabel('Company Name')
                            ->addValidator(new Zend_Validate_StringLength(0, 150))
                            ->setRequired(true);

        $website = $this->createElement('text', 'website')
                        ->setLabel('URL')
                        ->setDescription('The organization\'s website address, for example, http://example.com')
                        ->addValidator(new Zend_Validate_StringLength(0, 200))
                        ->addValidator(new BV_Validate_Uri());

        $description = $this->createElement('textarea', 'description')
                            ->setLabel('Description')
                            ->addValidator(new Zend_Validate_StringLength(0, 500))
                            ->setAttribs(array(
                                'rows' => 5,
                                'cols' => 80
                            ));
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');

      
        $this->addElements(array($companyName, $website, $description, $submit));
        $this->populate($org);    

        new BV_Filter_AddStripTagToElements($this);
        return $this;

    }
}


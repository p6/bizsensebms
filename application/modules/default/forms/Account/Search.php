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

class Core_Form_Account_Search
{
    public $db;

    function __construct() {

        $this->db = Zend_Registry::get('db');
    }

    public function getForm()
    {

        $db = $this->db;
        $form = new Zend_Form;
        $form->setAction('/account');
        $form->setMethod('get');
        $form->setName('search');

        $accountName = $form->createElement('text', 'accountName');
        $accountName->setLabel('Name');

        $city = $form->createElement('text', 'city');
        $city->setLabel('City');

        $state = $form->createElement('text', 'state');
        $state->setLabel('State');

        $country = $form->createElement('text', 'country');
        $country->setLabel('Country');

        $assignedTo = new Zend_Dojo_Form_Element_FilteringSelect('assignedTo');
        $assignedTo->setLabel('Accounts assigned to')
            ->setAutoComplete(true)
            ->setStoreId('accountStore')
            ->setStoreType('dojo.data.ItemFileReadStore')
            ->setStoreParams(array('url'=>'/user/jsonstore'))
            ->setAttrib("searchAttr", "email")
            ->setRequired(false);

        $branchId = new Zend_Dojo_Form_Element_FilteringSelect('branchId');
        $branchId->setLabel('Accounts Of Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branch_name")
                ->setRequired(false);

        $submit = $form->createElement('submit', 'submit')
                        ->setLabel('Search')
                        ->setAttrib("class", "submit_button");

	
        $form->addElements(
            array(
                $accountName, $city, $assignedTo, $branchId, $submit
            )
        );

        return $form;

    }

}


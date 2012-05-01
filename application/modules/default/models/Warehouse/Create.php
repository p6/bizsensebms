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

class Warehouse_Form_Create
{
    public $db;

    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    public function getForm()
    {
        $form = new Zend_Form;
        $form->setAction('/admin/warehouse/create');
        $form->setMethod('post');       
        
        $name = $form->createElement('text', 'name')
                    ->setRequired(true)
                    ->setLabel('Warehouse Name');
                    
        $addressLine1 = $form->createElement('text', 'addressLine1')
                            ->setLabel('Address Line 1')
                            ->setRequired(true);

        $addressLine2 = $form->createElement('text', 'addressLine2')
                            ->setLabel('Address Line 2')
                            ->setRequired(true);
        $addressLine3 = $form->createElement('text', 'addressLine3')
                            ->setLabel('Address Line 3');
        $addressLine4 = $form->createElement('text', 'addressLine4')
                            ->setLabel('Address Line 4');
        $city = $form->createElement('text', 'city')
                        ->setLabel('City')
                        ->setRequired(true);
        $state = $form->createElement('text','state')
                    ->setLabel('State')     
                    ->setRequired(true);
        $country = $form->createElement('text', 'country')
                    ->setLabel('Country')     
                    ->setRequired(true);
        $postalCode = $form->createElement('text', 'postalCode')
                    ->setLabel('Postal Code')     
                    ->setRequired(true);
        $phone = $form->createElement('text', 'phone')
                    ->setLabel('Phone');
     
        $fax = $form->createElement('text', 'fax')
                    ->setLabel('Fax');

        $description = $form->createElement('textarea', 'description')
                            ->setAttrib('rows','3')
                            ->setAttrib('cols','60')
                            ->setLabel('Description');
        $branchId = new Zend_Dojo_Form_Element_FilteringSelect('branchId');
        $branchId->setLabel('Belongs To Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branchName")
                ->setRequired(true);

        $incharge = new Zend_Dojo_Form_Element_FilteringSelect('uid');
        $incharge->setLabel('Warehouse Incharge')
            ->setAutoComplete(true)
            ->setStoreId('inchargeStore')
            ->setStoreType('dojo.data.ItemFileReadStore')
            ->setStoreParams(array('url'=>'/user/jsonlist'))
            ->setAttrib("searchAttr", "email")
            ->setRequired(true);

        $submit = $form->createElement('submit', 'submit');

        $form->addElements(array($name, $addressLine1, $addressLine2, $addressLine3, $addressLine4,
            $city, $state, $country, $postalCode, $phone, $fax, $description, $branchId, $incharge, $submit));
        return $form; 
    }
}



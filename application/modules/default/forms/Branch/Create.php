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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Branch_Create
{

    public function getForm()
    {
        $form = new Zend_Form;

        $form->setMethod('post');
        $form->setAction('/admin/branch/create');
        $branchName = $form->createElement('text', 'branch_name')
                                ->addValidator(new Zend_Validate_StringLength(0, 200))
                                ->addValidator(new Zend_Validate_Db_NoRecordExists('branch', 'branch_name'))
                                ->setRequired(true)
                                ->setLabel('Name');
        $addressLine1 = $form->createElement('text', 'address_line_1')
	                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                                ->setLabel('Address Line 1')
                                ->setRequired(true);

        $addressLine2 = $form->createElement('text', 'address_line_2')
	                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                                ->setLabel('Address Line 2')
                                ->setRequired(true);

        $addressLine3 = $form->createElement('text', 'address_line_3')
	                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                                ->setLabel('Address Line 3');

        $addressLine4 = $form->createElement('text', 'address_line_4')
	                        ->addValidator(new Zend_Validate_StringLength(0, 100))
                                ->setLabel('Address Line 4');

        $city   = $form->createElement('text', 'city')
	                ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('City')
                        ->setRequired(true);

        $state   = $form->createElement('text', 'state')
	                ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('State')
                        ->setRequired(true);

        $postalCode   = $form->createElement('text', 'postal_code')
	                ->addValidator(new Zend_Validate_StringLength(0, 10))
                        ->setLabel('Postal code')
                        ->setRequired(true);

        $country   = $form->createElement('text', 'country')
	                ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('Country')
                        ->setRequired(true);

        $phone   = $form->createElement('text', 'phone')
	                ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('Phone');

        $fax   = $form->createElement('text', 'fax')
	                ->addValidator(new Zend_Validate_StringLength(0, 100))
                        ->setLabel('Fax');

        $email   = $form->createElement('text', 'email')
	                ->addValidator(new Zend_Validate_StringLength(0, 320))
                        ->addValidator(new Zend_Validate_EmailAddress())
                        ->setLabel('Email');

        $serviceTax = $form->createElement('text', 'service_tax_number')
                            ->setLabel('Service tax number')
                            ->addValidator(new Zend_Validate_StringLength(0,40));

        $tin = $form->createElement('text', 'tin')
                            ->setLabel('TIN')
                            ->setDescription('Tax identification number - VAT')
                            ->addValidator(new Zend_Validate_StringLength(0,40));


		$parentBranch = new Zend_Dojo_Form_Element_FilteringSelect('parent_branch_id');
        $parentBranch->setLabel('Parent Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branch_name")
                ->setRequired(true);


        $branchManager = $form->createElement('text', 'branch_manager')
                        ->setLabel('Branch Manager');

        $branchManager = new Zend_Dojo_Form_Element_FilteringSelect('branch_manager');
        $branchManager->setLabel('Branch Manager')
            ->setAutoComplete(true)
            ->setStoreId('userStore')
            ->setStoreType('dojo.data.ItemFileReadStore')
            ->setStoreParams(array('url'=>'/user/jsonstore'))
            ->setAttrib("searchAttr", "email")
            ->setRequired(true);

	    $description = $form->createElement('textarea', 'description')
	                        ->addValidator(new Zend_Validate_StringLength(0, 250))
                            ->setLabel('Description')
                            ->setAttribs(
                                array(
                                    'rows' => 5,
                                    'cols' => 40
                                )
                            );

        $submit = $form->createElement('submit', 'submit')
                        ->setAttrib('class', 'submit_button');

        $form->addElements(
            array(
                $branchName, $addressLine1, $addressLine2, 
                $addressLine3, $addressLine4, $city, 
                $state, $postalCode,
                $country, $phone, $fax, $email, 
                $serviceTax, $tin, $parentBranch, 
                $branchManager, $description, $submit
            )
        ); 

        return $form;
    }
}    

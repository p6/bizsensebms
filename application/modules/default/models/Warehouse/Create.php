<?php
/*
 * Create warehouse form
 *
 *
 * LICENSE: GNU GPL V3
 *
 * This source file is subject to the GNU GPL V3 license that is bundled
 * with this package in the file license
 * It is also available through the world-wide-web at this URL:
 * http://bizsense.binaryvibes.co.in/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryvibes.co.in so we can send you a copy immediately.
 *
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @license    http://bizsense.binaryvibes.co.in/license   
 * @version    $Id:$
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



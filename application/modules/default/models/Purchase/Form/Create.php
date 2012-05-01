<?php
/*
 * Purchase create form
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

class Purchase_Form_Create
{
    public $db;

    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    public function getForm()
    {
        $form = new Zend_Form;
        $form->setAction('/purchase/create');
        $form->setMethod('post');

        $form->setAttrib('id', 'purchaseForm');

        $form->setIsArray(false);
        $form->addSubForm($this->getProductsSubForm(), 'products');
        $form->addSubForm($this->getHeaderSubForm(), 'header');
        $form->addSubForm($this->getTermsSubForm(), 'terms');

        $submit = $form->createElement('submit', 'submit')
                        ->setLabel('Create Purchase Entry');

        $form->addElement($submit);
        return $form;                 
    }

    public function getProductsSubForm()
    {

        $productsForm = new Zend_Form_SubForm;
        $productsForm->setIsArray(false);

        $button = $productsForm->createElement('button', 'more')
                        ->setLabel('Add Items')
                        ->setAttrib('onclick', 'moreWidgets()');   
        $productsForm->addElements(array($button));
        
        $productsForm->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table', 'id'=>'productsTable')),
            'Form'
        ));     
        $productsForm->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            array('decorator' => array('td' => 'HtmlTag'), 'options' => array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('decorator' => array('tr' => 'HtmlTag'), 'options' => array('tag' => 'tr')),
        )); 

        $productsForm->more->setDecorators(array(
            array(
                'decorator' => 'ViewHelper',
                'options' => array('helper' => 'formButton')),
            array(
                'decorator' => array('td' => 'HtmlTag'),
                'options' => array('tag' => 'td', 'colspan' => 2)),
            array(
                'decorator' => array('tr' => 'HtmlTag'),
                'options' => array('tag' => 'tr')),
        ));

        return $productsForm;
    }

    public function getHeaderSubForm()
    {
        
        $form = new Zend_Form_SubForm;
          $form->setIsArray(false);
 
        $subject = $form->createElement('text', 'subject')
                        ->setLabel('Subject')
                        ->setRequired(true);

        $vendorReferenceCode = $form->createElement('text', 'vendorReferenceCode')
                                        ->setLabel('Vendor Sale Reference');
        $contactId = new Zend_Dojo_Form_Element_FilteringSelect('contactId');
        $contactId->setLabel('Vendor Contact Person')
                    ->setAutoComplete(true)
                    ->setStoreId('assistantStore')
                    ->setStoreType('dojo.data.ItemFileReadStore')
                    ->setStoreParams(array('url'=>'/contact/jsonlist'))
                    ->setAttrib("searchAttr", "contact");

        $accountId = new Zend_Dojo_Form_Element_FilteringSelect('accountId');
        $accountId->setLabel('Consignor')
                ->setAutoComplete(true)
                ->setStoreId('stateStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/account/jsonlist'))
                ->setAttrib("searchAttr", "account")
                ->setRequired(true);

        $consigneeId = new Zend_Dojo_Form_Element_FilteringSelect('consigneeId');
        $consigneeId->setLabel('Consignee')
                ->setAutoComplete(true)
                ->setStoreId('consigneeStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branchName")
                ->setRequired(true);

        $deliveryPointId = new Zend_Dojo_Form_Element_FilteringSelect('deliveryPointId');
        $deliveryPointId->setLabel('Delivery Point')
                ->setAutoComplete(true)
                ->setStoreId('deliveryPointStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/warehouse'))
                ->setAttrib("searchAttr", "name")
                ->setRequired(false);

        $useBelowDeliveryPoint = $form->createElement('checkbox', 'useBelowDeliveryPoint')
                                      ->setLabel('Use Below Delivery Point');

        $otherDeliveryPoint = $form->createElement('textarea', 'otherDeliveryPoint')    
                                    ->setAttribs(array(
                                        'rows' => 5,
                                        'cols' => 30
                                      ))
                                    ->setLabel('Other Delivery Point')
                                    ->setAttrib('disabled', 'disabled'); 

        $form->addElements(array($subject, $vendorReferenceCode, $contactId, $accountId, $consigneeId, 
            $deliveryPointId, $useBelowDeliveryPoint, $otherDeliveryPoint)); 
        return $form;
    }

    public function getTermsSubForm()
    {

        $form = new Zend_Form_SubForm;
        $form->setIsArray(false);


        $deliveryTerms = $form->createElement('textarea', 'deliveryTerms')
                                ->setLabel('Delivery Terms')
                                ->setAttribs(array('rows'=>5, 'cols'=>30));

        $paymentTerms = $form->createElement('textarea', 'paymentTerms')
                                ->setLabel('Payment Terms')
                                ->setAttribs(array('rows'=>5, 'cols'=>30));

        $otherTerms = $form->createElement('textarea', 'otherTerms')
                                ->setLabel('Other Purchase Terms')
                                ->setAttribs(array('rows'=>5, 'cols'=>30));

        $shippingInstructions = $form->createElement('textarea', 'shippingInstructions')
                                ->setLabel('Shipping Instructions')
                                ->setAttribs(array('rows'=>5, 'cols'=>30));

        $purchaseNotes = $form->createElement('textarea', 'purchaseNotes')
                                ->setLabel('Purchase Notes')
                                ->setAttribs(array('rows'=>5, 'cols'=>30));

        $internalPurchaseNotes = $form->createElement('textarea', 'internalPurchaseNotes')
                                ->setLabel('Internal Purchase Notes')
                                ->setAttribs(array('rows'=>5, 'cols'=>30));

        $form->addElements(array($deliveryTerms, $paymentTerms, 
            $otherTerms, $shippingInstructions, $purchaseNotes, $internalPurchaseNotes));

        return $form;
    }
   
 
}

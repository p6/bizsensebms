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
 * @category BizSense
 * @package  Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Form_Invoice_CreateServiceInvoice extends Zend_Form
{
    public function init()
    {
        $invoiceDate = new Zend_Dojo_Form_Element_DateTextBox('date');
        $invoiceDate->setRequired(true)
                    ->setLabel('Invoice Date');
        $this->addElement($invoiceDate);

        $invoiceTo = $this->createElement(
            'radio', 'to_type', array(
                'label'     =>  'Invoice to',
                'required'  => true,
            )
        );
        
        $invoiceTo->addValidator(new Core_Model_Opportunity_Validate_CustomerType());
        
        $invoiceTo->addMultiOptions(
            array(
                Core_Model_Invoice::TO_TYPE_ACCOUNT => 'Account', 
                Core_Model_Invoice::TO_TYPE_CONTACT =>'Contact'
            )
        );
        
        $this->addElement($invoiceTo);

        $accountId = new Core_Form_Account_Element_Account;
        $accountElement = $accountId->getElement();
        $accountElement->setStoreParams(array('url'=>'/account/jsonstore'));
        $this->addElement($accountElement); 

        $contactId = new Core_Form_Contact_Element_Contact;
        $contactElement = $contactId->getElement();
        $this->addElement($contactElement); 

        $campaignElementContainer = new Core_Form_Campaign_Element_Campaign;
        $campaignId = $campaignElementContainer->getElement();
        $this->addElement($campaignId);

        $branchElement = new Core_Form_Branch_Element_Branch;
        $branchId = $branchElement->getElement();
        $branchId->setRequired(true);
        $this->addElement($branchId);

        $this->addElement('text', 'purchase_order', array(
                'label' => 'Purchase order number',
            )
        );


        $this->addElement('textarea', 'notes', array(
                'label' => 'Invoice notes',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );

        $this->addElement('textarea', 'payment_terms', array(
                'label' => 'Payment terms',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );

        $this->addElement('textarea', 'delivery_terms', array(
                'label' => 'Delivery terms',
                'attribs' => array('rows'=>5, 'cols'=>40),
            )
        );

        $this->addElement('submit', 'submit', 
            array(
                'ignore' => true,
                'class' => 'submit_button'
            )
        );
    }
}


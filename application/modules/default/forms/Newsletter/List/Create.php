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
class Core_Form_Newsletter_List_Create extends Zend_Form
{
    public function init() 
    {
        $name = $this->createElement('text', 'name')
                                ->setRequired(true)
                                ->addValidator(new Zend_Validate_StringLength(1,200))
                                ->setLabel('Name');

        $description = $this->createElement('text', 'description')
                                ->addValidator(new Zend_Validate_StringLength(1,250))
                                ->setLabel('Description');

        $showInCustomerPanel = $this->createElement('checkbox', 'show_in_customer_portal')
                            ->setDescription('If checked the list user will be displayed in control panel')
                            ->setValue(1)
                            ->setLabel('Show in customer panel'); 

        $handleAutomaticBounces = $this->createElement('checkbox','auto_bounce_handle')
                            ->setDescription('Enable automatically bounce handle')
                            ->setValue(1)
                            ->setLabel('Automatically bounce handle');
        
        $submit = $this->createElement('submit', 'submit')
                        ->setIgnore(true)
                       ->setAttrib('class', 'submit_button');

        $this->addElements(array($name, $description, $showInCustomerPanel, $handleAutomaticBounces, $submit)); 
    }
}    

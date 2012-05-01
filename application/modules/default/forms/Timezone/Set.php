<?php
/*
 * overview
 *
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Timezone_Set
{
    public $db;
    
    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }
        
    public function getForm()
    {
        $form = new Zend_Form;
        $form->setAction('/admin/timezone/set'); 
        $form->setMethod('post');  
            
        $timezone = BV_Form_Element_Timezone::getElement();
        
        $timezoneSet = $this->db->fetchOne("SELECT timezone FROM settings");
        $timezone->setValue($timezoneSet);
        $submit = $form->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');
        
        $form->addElements(array($timezone, $submit));
        return $form;
    } 
}



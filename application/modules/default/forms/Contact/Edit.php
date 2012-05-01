<?php
/*
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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/** 
 * Contact edit form
 */
class Core_Form_Contact_Edit extends Core_Form_Contact_Create
{
    public $db;
    public $contactId;

    function __construct($contactId = null) 
    {

        $this->db = Zend_Registry::get('db');
        if (is_numeric($contactId)) {
            $this->contactId = $contactId;            
        }
        parent::__construct();
    }

   
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $contact = new Core_Model_Contact($this->contactId);
        $contact = $contact->fetchAsArray();
       
        if ($contact['reports_to']) {
            
        }
        parent::init();
         $workEmail = $this->createElement('text', 'work_email')
                            ->setLabel('Work Email')
                            ->addValidator(new Zend_Validate_EmailAddress())
	                        ->addValidator(new Zend_Validate_StringLength(0, 320))
	                        ->addValidator(new Zend_Validate_Db_NoRecordExists(
                                'contact', 'work_email',array(
                                'field' => 'work_email', 'value' => $contact['work_email'] )));
         $this->addElements(array($workEmail));
         
         $contactGroup = $this->addDisplayGroup(array('work_phone', 'home_phone', 'mobile', 'fax', 'work_email', 
            'other_email'), 'contact');
         $contactGroup->getDisplayGroup('contact')->setLegend('Contact details');
          
        #var_dump($contact);
        $this->populate($contact);
      
        /*
         * Dojo sets date to 11/30/1899 if 0000-00-00 is passed to populate method of Zend_Form
         * Therefore set the value to '' if the date is not valid
         
        $birthdayInModel = $contact['birthday'];
        if (! (Zend_Date::isDate($birthdayInModel)) ) {
         #   $this->getElement('birthday')->setValue('');
        }
 
        if ($birthdayInModel == '0000-00-00') {
            $this->getElement('birthday')->setValue('');
        }
        */ 
    }   

     
 
  }

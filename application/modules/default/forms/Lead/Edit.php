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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Form_Lead_Edit extends Core_Form_Lead_Create
{
    protected $_leadId;

    function __construct($leadId) 
    {
        $this->_leadId = $leadId;
        parent::__construct();
    }
    
    public function init()
    {
        parent::init();
        
        $leadModel = new Core_Model_Lead($this->_leadId);
        $leadDetails = $leadModel->fetch();
        
        $this->getElement('submit')->setLabel('Edit Lead');
        $mobile = $this->createElement('text', 'mobile')
                       ->addValidator(new Zend_Validate_StringLength(3, 40))
                       ->setLabel('Mobile')
                       ->addValidator(new Zend_Validate_Db_NoRecordExists(
                          'lead', 'mobile',array(
                          'field' => 'mobile', 'value' => $leadDetails['mobile'] )));

        $email = $this->createElement('text', 'email')
                      ->setLabel('Email')
                      ->addValidator(new Zend_Validate_StringLength(0, 320))
                      ->addValidator(new Zend_Validate_Db_NoRecordExists(
                         'lead', 'email',array(
                         'field' => 'email', 'value' => $leadDetails['email'] )))                           
                      ->addValidator(new Zend_Validate_Db_NoRecordExists(
                         'contact', 'work_email',array(
                         'field' => 'work_email', 'value' => $leadDetails['email'] )))
                      ->addValidator(new Zend_Validate_EmailAddress());
        $this->addElements(array($email, $mobile));     
        
        $contactGroup = $this->addDisplayGroup(array('home_phone', 'work_phone',
            'mobile', 'do_not_call', 'fax', 'email', 
            'email_opt_out'), 'contact');
            
        $lead = new Core_Model_Lead($this->_leadId);
        $rows = $lead->fetch();
        $casted = (array)$rows;
        $this->populate($casted);
    }
}

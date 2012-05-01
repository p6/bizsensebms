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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 */

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Newsletter_Subscriber_Edit extends 
        Core_Form_Newsletter_Subscriber_Create
{
    /**
     *@var string subscriber model
     */
    protected $_subscriberId;
    
    public function __construct($subscriberId)
    {
        $this->_subscriberId = $subscriberId;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $subscriberModel = new Core_Model_Newsletter_Subscriber;
        $defaultValues = $subscriberModel->setSubscriberId($this->_subscriberId)
                                    ->fetch();
        $email = $this->createElement('text', 'email')
                            ->setLabel('Email')
                            ->setRequired(true)
                            ->addValidator(new Zend_Validate_StringLength(0, 320))
                            ->addValidator(new Zend_Validate_EmailAddress())
                            ->addValidator(new Zend_Validate_Db_NoRecordExists(
                                'subscriber', 'email',array(
                                'field' => 'email', 'value' => $defaultValues['email'] ))); 
        $this->addElement($email);
               
        if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::CONFIRMED) {
            $defaultValues['status'] = Core_Model_Newsletter_Subscriber::CONFIRMED;
        }
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::UNCONFIRMED) {
           $defaultValues['status']= Core_Model_Newsletter_Subscriber::UNCONFIRMED;
        }  
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::ACTIVE) {
            $defaultValues['status']= Core_Model_Newsletter_Subscriber::ACTIVE;
        } 
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::BLOCKED) {
            $defaultValues['status']= Core_Model_Newsletter_Subscriber::BLOCKED;
        } 
        if ($defaultValues['format']) {
            $defaultValues['format'] = 1;
        }
        $this->populate($defaultValues);
    }
}
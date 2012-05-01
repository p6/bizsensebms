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
class Core_Form_Newsletter_List_Subscriber_Edit extends 
        Core_Form_Newsletter_List_Subscriber_Create
{
    /**
     *@var int listId
     */
    protected $_listId;

    /**
     *@var string subscriber model
     */
    protected $_subscriberId;
    
    public function __construct($listId,$subscriberId)
    {
        $this->_subscriberId = $subscriberId;
        $this->_listId = $listId;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $subscriberModel = new Core_Model_Newsletter_Subscriber;
        $defaultValues = $subscriberModel->setSubscriberId($this->_subscriberId)
                                    ->fetch();
        
        if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::CONFIRMED) {
            $defaultValues['status']= "Confirmed";
        }
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::UNCONFIRMED) {
           $defaultValues['status']= "Unconfirmed";
        }  
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::ACTIVE) {
            $defaultValues['status']="Active";
        } 
        else if ($defaultValues['status'] == Core_Model_Newsletter_Subscriber::BLOCKED) {
            $defaultValues['status']= "Blocked";
        } 
        $this->populate($defaultValues);
    }
}

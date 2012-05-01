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
class Core_Service_WebService_Rest_Newsletter_Subscriber
{
    /**
     * @var object Core_Model_Newsletter_Subscriber
     */
    protected $_model;

    /**
     * @return object Core_Model_Newsletter_Subscriber
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = new Core_Model_Newsletter_Subscriber;
        }
        return $this->_model;
    }
        
    /** 
     * Create a row in the subscriber table
     */
    public function create($data)
    {
        $created = "";
        $form = new Core_Form_Newsletter_Subscriber_Create();

        if ($form->isValid($data)) {
           return $this->getModel()->create($form->getValues());        
        }else {
            return $form->getMessages();
        }
    }
    
    /**
     * @return array collection of subscribers
     */
    public function fetchAll()
    {
        return $this->getModel()->fetchAll();
    }
    
    /**
     * @return array collection of subscribers
     */
    public function fetch($subscriberId)
    {
        $this->getModel()->setSubscriberId($subscriberId);
        return $this->getModel()->fetch();
    }
    
    /**
     * @param array $data
     * @return array collection
     */
    public function edit($data, $subscriberId)
    {
        $form = new Core_Form_Newsletter_Subscriber_Edit($subscriberId);
        if ($form->isValid($data)) {
           $this->getModel()->setSubscriberId($subscriberId);
           $this->getModel()->edit($data);  
           return $this->fetch($subscriberId);     
        }else {
            return $form->getMessages();
        }       
    }
    
    /** 
     * Delete a row in the subscriber table
     */
    public function delete($subscriberId)
    {
        $this->getModel()->setSubscriberId($subscriberId);
        $result = $this->getModel()->delete();
        return $result;
    }

    
}

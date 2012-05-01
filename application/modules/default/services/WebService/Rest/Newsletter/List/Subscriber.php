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
class Core_Service_WebService_Rest_Newsletter_List_Subscriber
{
    /**
     * @var object Core_Model_Newsletter_List_Subscriber
     */
    protected $_model;

    /**
     * @return object Core_Model_Newsletter_List_Subscriber
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = new Core_Model_Newsletter_List_Subscriber;
        }
        return $this->_model;
    }
    
    /**
     * @return array collection
     */
    public function fetch($listSubscriberId)
    {
        $this->getModel()->setListSubscriberId($listSubscriberId);
        return $this->getModel()->fetch();
    }


    /**
     * @return array collection of lists
     */
    public function fetchAll()
    {
        return $this->getModel()->fetchAll();
    }
  
    /**
     * @param array $data
     * @return array collection
     */
    public function create($data)
    {
      return $this->getModel()->create($data['list_id'], $data['subscriber_id']);
    }

    /**
     * @param array $data
     * @return array collection
     */
    public function edit($email, $data)
    {
        $listId = $this->getModel()->getModel()->getlistId();
        $this->getModel()->editByListIdAndEmail($listId, $email, $data);
        return $this->fetch($data['email']);
    }
    
    /** 
     * Delete a row in the list subscriber table
     */
    public function delete($listSubscriberId)
    {
        $this->getModel()->setListSubscriberId($listSubscriberId);
        $result = $this->getModel()->delete();
        return $result;
    }
}

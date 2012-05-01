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
class Core_Service_WebService_Rest_Newsletter_List
{
    /**
     * @var object Core_Model_Newsletter_List
     */
    protected $_model;

    /**
     * @return object Core_Model_Newsletter_List
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = new Core_Model_Newsletter_List;
        }
        return $this->_model;
    }

    public function setListId($listId)
    {
        $showInCustomerPortal = $this->getModel()->shownInCustomerPortal($listId);
        $this->getModel()->setListId($listId);
        return $this;
    }

    /**
     * @return array collection
     */
    public function fetch()
    {
       return $this->getModel()->fetch();
    }


    /**
     * @return array collection of lists
     */
    public function fetchAll()
    {
        $criteria = array(
            'show_in_customer_portal' => Core_Model_Newsletter_List::SHOW_IN_CUSTOMER_PORTAL
        );
        return $this->getModel()->fetchAll($criteria);
    }
  
    /**
     * @param array $data
     * @return array collection
     */
    public function create($data)
    {
        $data['show_in_customer_portal'] = 1;
        $this->getModel()->create($data);
        return $this->getModel()->fetch();
    }

    /**
     * @param array $data
     * @return array collection
     */
    public function edit($data)
    {
        $this->getModel()->edit($data);
        return $this->getModel()->fetch();
    }

    /**
     * @return mixed whether of not the list was deleted
     */
    public function delete()
    {
        $listId = (int) $this->getModel()->getListId();
        if ($listId) {
            return $this->getModel()->delete();    
        } else {
            return false;
        }
    }
}

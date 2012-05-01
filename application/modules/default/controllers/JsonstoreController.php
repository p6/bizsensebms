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
 * You can contact Binary Vibes Information Technologies Pvt. 
 * Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
class JsonstoreController Extends Zend_Controller_Action
{
    public $db;

    /**
     * Initialize the controller
     */  
    public function init()
    {
        $this->db = Zend_Registry::get('db');
    }

    /*
     * @Zend_Dojo_Data
     * List of all branches
     */
    public function branchAction()
    {
        $db = $this->db;

        $this->_helper->layout->disableLayout();

        $sql = "SELECT branch_name, branch_id as id FROM branch";
        $result = $db->fetchAll($sql);

        $this->view->result = $result;
    }

    /**
     * Tax type Zend_Dojo_Data items
     */
    public function taxtypeAction()
    {
        $db = $this->db;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $sql = "SELECT tax_type_id, percentage, name, description FROM tax_type";
        $result = $db->fetchAll($sql);
        $items = (array) $result;

        $data = new Zend_Dojo_Data('tax_type_id', $items);
        $data->setLabel('name');
        $this->_helper->autoCompleteDojo($data);

    }

    /**
     * Zend_Dojo_Data items of profiles
     */
    public function profileAction()
    {
        $db = $this->db;
        $this->_helper->layout->disableLayout();
        $select = $db->select();
        $select->from(array('p'=>'profile'), '*')
                ->joinLeft(array('u'=>'user'), 'u.user_id = p.user_id', array('u.email'))
                ->where('u.type = 1');
        $result = $db->fetchAll($select);
        $items = (array) $result;

        $data = new Zend_Dojo_Data('profile_id', $items);
        $data->setLabel('first_name');

        $this->_helper->autoCompleteDojo($data);
    }


    /**
     * Send JSON Data for consumption by Dojo FilteringSelect widget
     */
    public function primaryroleAction()
    {
        $db = $this->db;
        $this->_helper->layout->disableLayout();
        $select = $db->select();
        $select->from(array('r'=>'role'), '*');
               # ->where('role_id > 3');
        $result = $db->fetchAll($select, null, Zend_Db::FETCH_ASSOC);
        $items = $result;
        $data = new Zend_Dojo_Data('role_id', $items);
        $data->setLabel('name');

        $this->_helper->autoCompleteDojo($data);

    }
}





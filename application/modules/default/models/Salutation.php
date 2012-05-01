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

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/** 
 * Salutation
*/
class Core_Model_Salutation
{
    public $db;

    protected $_salutation_id;

    public function __construct($salutationId)
    {
        $this->db = Zend_Registry::get('db');
        if (is_numeric($salutationId)) {
            $this->_salutation_id = $salutationId;
        }
    }
    
    /**
     * @param int $quoteId the invoice ID
     * @return fluent interface
     */
    public function setSalutationId($salutationId)
    {
        $this->_salutation_id = $salutationId;
        return $this;
    }

    public function fetch()
    {
        $select = $this->db->select();
        $select->from(array('s'=>'salutation'), array('salutation_id', 'name', 'description'));
        $select->where('salutation_id = ?', $this->_salutation_id);
        $result = $this->db->fetchRow($select);
        return $result;
    }    

    public function delete()
    {
        $where = $this->db->quote($this->_salutation_id, 'INTEGER');
        $result = $this->db->delete('salutation', "salutation_id = $where");
        return $result;
 
    }
}



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
 * an electronic mail 
 * to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @category    BizSense
 * @package     Core
 * @copyright   Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd.
 * @version    $Id:$
 */

/**
 * @category    BizSense
 * @package     Core
 * @subpackage  Core_Model
 * @copyright   Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt Ltd
 */
class Core_Model_Lead_Status extends BV_Model_Essential_Abstract
{
    protected $_leadStatusId;
   
    public function __construct($leadStatusId = null)
    {
        parent::__construct();
        if (is_numeric($leadStatusId)) {
            $this->_leadStatusId = $leadStatusId;
        }
    }
 
    /*
     * @return Zend_Db_Select
     * Object to pass to Zend_Paginator
     */    
    public function getIndexSelectObject()
    {
        $select = $this->db->select();
        $select->from(array('ls'=>'lead_status'),
                array('*') );
        return $select;     
    }       
    
    /*
     * Fetch all the lead status entries
     */
    public function fetchAll()
    {
        $select = $this->getIndexSelectObject();
        $result = $this->db->fetchAll($select);
        return $result;     
    }
    
    /*
     * Fetch a single lead status item
     */
    public function fetch()
    {
        $select = $this->getIndexSelectObject();
        $select->where('lead_status_id = ?', $this->_leadStatusId);
        $result = $this->db->fetchRow($select);
        return $result;
    }

    /*
     * Some client code requires data in array type
     */     
    public function fetchAsArray()
    {
        $select = $this->getIndexSelectObject();
        $select->where('lead_status_id = ?', $this->_leadStatusId);
        $result = $this->db->fetchRow($select, null, Zend_Db::FETCH_ASSOC);
        return $result;
    }
        
   /* 
     * Deletes a row in the leadstatus table
     */
    public function delete()
    {
        $where = $this->db->quote($this->_leadStatusId, 'INTEGER');
        $result = $this->db->delete('lead_status', "lead_status_id = $where");
        return $result;
    }
      
}



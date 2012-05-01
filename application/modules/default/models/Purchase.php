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
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Model_Purchase
{
    public $db;
    public $purchaseId;

    public function __construct($purchaseId = null)
    {
        $this->db = Zend_Registry::get('db');
        $this->purchaseId = $purchaseId;    
    }
    public function getDetails()
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();
        $select->from(array('p'=>'purchase'),
                        array('*'))
                ->joinLeft(array('a'=>'account'),
                    'p.vendorId = a.accountId',
                    array('a.accountName'))
                ->joinLeft(array('b'=>'branch'),
                    'b.branchId = p.consigneeBranch', array('b.branchName'))
                ->joinLeft(array('c'=>'contact'),
                        'p.vendorContactId = c.contactId', array('c.firstName as vendorContact'))
                ->joinLeft(array('u'=>'user'), 
                        'u.uid = p.createdBy', array('u.email'))
                ->where('p.id = ?', $this->purchaseId);
        $result = $db->fetchRow($select);
        
        return $result; 
    }
    
    public function getItemDetails()
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();
        $select->from(array('pp'=>'purchaseProducts'),
                        array('*'))
                ->joinLeft(array('p'=>'purchase'),
                    'pp.purchaseId = p.id')
                ->joinLeft(array('pr'=>'product'), 
                    'pp.productId = pr.productId', array('pr.productName'))
                ->joinLeft(array('tt'=>'taxType'),
                    'tt.id = pp.productId', array('tt.name as taxName', 'tt.percentage'))
                ->where('pp.purchaseId = ?', $this->purchaseId);
 
        $sql = $select->__toString();
        $result = $db->fetchAll($select);
        
        return $result; 
 
    }

    /**
     * Initiate index search and sort processing object
     * @return Zend_Db_Select object
     */
    public function getListingSelectObject($search, $sort)
    {
        $process = new Purchase_Index;
        $processed = $process->getListingSelectObject($search, $sort);
        return $processed;
    }


}



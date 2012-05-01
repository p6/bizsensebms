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

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Quote_QuoteStatus extends Core_Model_Abstract
{
    /**
     * @var int quote status ID
     */
    protected $_quoteStatusId;
   
    /**
     * constant for quote context 
     */
    const QUOTE_CONTEXT_IN_PROGRESS = 0;
    const QUOTE_CONTEXT_WON = 1;
    const QUOTE_CONTEXT_LOST = 2;
    
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */ 
    protected $_dbTableClass = 'Core_Model_DbTable_QuoteStatus';

    public function __construct($quoteStatusId = null)
    {
        if (is_numeric($quoteStatusId)) {
            $this->_quoteStatusId = $quoteStatusId;
        }
        return $this;
    }

    /**
     * @param int $quoteStatusId
     * @return fluent interface
     */
    public function setQuoteStatusId($quoteStatusId)
    {
        if (is_numeric($quoteStatusId)) {
            $this->_quoteStatusId = $quoteStatusId;
        }
        return $this;
    }

    /**
     * @param array $data
     * @return int the newly created quote status ID
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $result = $table->insert($data);
        return $result;
    }

    /**
     * @return array the quote status record
     */
    public function fetch()
    {
        if (!is_numeric($this->_quoteStatusId)){
            return false;
        }
        $table = $this->getTable();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('quote_status'=>'quote_status'))
                ->where('quote_status_id = ?', $this->_quoteStatusId);
        $result =  $table->fetchRow($select)->toArray();
        return $result;
    }

    /**
     * @param array $data
     * @return int 
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('quote_status_id = ?', $this->_quoteStatusId);
        $result = $table->update($data, $where);
        return $result;
    }

    /**
     * @return int the number of records deleted
     */
    public function delete()
    {   
        $table = $this->getTable();
        $where = $table->getAdapter()
                    ->quoteInto('quote_status_id = ?', $this->_quoteStatusId);
        $result = $table->delete($where);
        return $result;
    }
}

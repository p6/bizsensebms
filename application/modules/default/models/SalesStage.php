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
class Core_Model_SalesStage extends Core_Model_Abstract
{
    const SALES_STAGE_STATUS_ONGOING = 0;
    const SALES_STAGE_STATUS_WON = 1;
    const SALES_STAGE_STATUS_LOST = 2;

    /**
     * @var int sale stage ID
     */
    protected $_salesStageId;

    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_SalesStage';

    /**
     * @param int $salesStageId
     * @return fluent interfacce
     */
    public function setSalesStageId($salesStageId)
    {
        $this->_salesStageId = $salesStageId;
        return $this;
    }

    /**
     * @param array $data
     * @return int the newly created sales stage ID
     */
    public function create($data = array())
    {
        $table = $this->getTable();
        $salesStageId = $table->insert($data);
        $this->_salesStageId = $salesStageId;
        return $salesStageId;
    }

    /**
     * @return array the sales stage record
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()
                    ->where('sales_stage_id = ?', $this->_salesStageId);
        $result = $table->fetchRow($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * @param array $data
     * @return int 
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $where = $table->getAdapter()->quoteInto('sales_stage_id = ?', $this->_salesStageId);
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
                    ->quoteInto('sales_stage_id = ?', $this->_salesStageId);
        $result = $table->delete($where);
        return $result;
    }
}

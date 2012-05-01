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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Newsletter_Report extends Core_Model_Abstract
{
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_Newsletter_MessageQueue';
    
    /**
     * @return int total
     */
     public function getGraphReport($data)
     {       
        $table = $this->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
               
        $select->from(array('mq'=>'message_queue'),array('count(*) as count'))
            ->joinLeft(array('s' => 'subscriber'),
                's.subscriber_id = mq.subscriber_id',
                array('domain','count(domain) as dc'))
            ->group('s.domain')
            ->order(array('dc DESC'))
            ->limit('5');
        
        if(!(empty($data['date_from'])) and !(empty($data['date_to']))) {
            if($data['start_time']) {
                $startFromDate = $data['date_from'] . $data['start_time'];
            }
            else {
                $startFromDate = $data['date_from'] . 'T00:00:00';
            }
            $startFrom= new Zend_Date($startFromDate);
            $startFromTimeStamp = $startFrom->getTimeStamp();

            if($data['end_time']) {
                $startToDate = $data['date_to'] . $data['end_time'];
            }
            else {
                $startToDate = $data['date_to'] . 'T23:59:59';
            }
            $startTo= new Zend_Date($startToDate);
            $startToTimeStamp = $startTo->getTimeStamp();

            $select->where("mq.sent_time between '$startFromTimeStamp' and '$startToTimeStamp'");
        }
        
        if ($data['domain']) {
            $select->where('s.domain like ?', '%' . $data['domain'] . '%'); 
        }  
        
        if ($data['status'] != '') {
            $select->where('mq.status = ?', $data['status']); 
        }  
        else {
            $select->where('mq.status = ?',Core_Model_Newsletter_Message_Queue::MESSAGE_NOT_SENT); 
        }
        $result = $table->fetchAll($select);
        if ($result) {
           $result = $result->toArray();
        } 
        return $result;
     }
}

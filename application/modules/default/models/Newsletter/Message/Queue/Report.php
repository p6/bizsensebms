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
class Core_Model_Newsletter_Message_Queue_Report extends Core_Model_Index_Abstract
{
    /**
     * @return Zend_Paginator object
     */
    public function getPaginator()
    {
        $db = Zend_Registry::get('db');

        $table = $this->_model->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
  
        $select->from(array('mq'=>'message_queue'),
                    array(
                        'message_queue_id', 
                        'status', 
                        'message_id', 
                        'list_id',
                        'subscriber_id',
                        'sent_time'
                    )
                )
               ->joinLeft(
                    array('s'=>'subscriber'), 
                    's.subscriber_id = mq.subscriber_id', 
                    array('s.email','domain')
                )
                ->joinLeft(
                    array('m'=>'message'), 
                    'm.message_id = mq.message_id', 
                    array('message_id','subject')
                );
                
               
        $search = $this->_search; 
        $sort = $this->_sort;
        /**
         * Sort data    
         */
        switch ($sort) {

            case "emailAsc" :
                $select->order('email');
            break;
            case "emailDes" :
                $select->order('email DESC');
            break;

            case "messageDes" :
                $select->order('m.subject DESC');
            break;

            case "messageAsc" :
                $select->order('m.subject');
            break;
    
            case "domainDes" :
                $select->order('s.domain DESC');
            break;

            case "domainAsc" :
                $select->order('s.domain');
            break;

        }

        /**
         * Search 
         */ 
         
        if(!(empty($search['date_from'])) and !(empty($search['date_to']))) {
            if($search['start_time']) {
                $startFromDate = $search['date_from'] . $search['start_time'];
            }
            else {
                $startFromDate = $search['date_from'] . 'T00:00:00';
            }
            $startFrom= new Zend_Date($startFromDate);
            $startFromTimeStamp = $startFrom->getTimeStamp();

            if($search['end_time']) {
                $startToDate = $search['date_to'] . $search['end_time'];
            }
            else {
                $startToDate = $search['date_to'] . 'T23:59:59';
            }
            $startTo= new Zend_Date($startToDate);
            $startToTimeStamp = $startTo->getTimeStamp();

            $select->where("sent_time between '$startFromTimeStamp' and '$startToTimeStamp'");
        }
        
        if ($search['domain']) {
            $select->where('s.domain like ?', '%' . $search['domain'] . '%'); 
        }  
        
        if ($search['status'] != '') {
            $select->where('mq.status = ?', $search['status']); 
        }  
                       
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
}

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
class Core_Model_Newsletter_Message_Queue_Index extends Core_Model_Index_Abstract
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
                        'subscriber_id'
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
         * Search 
         */
        
        if ($search['email']) {
            $select->where('s.email like ?', '%' . $search['email'] . '%'); 
        }   
        
        if ($search['subject']) {
            $select->where('m.subject like ?', '%' . $search['subject'] . '%'); 
        } 
         
        if ($search['domain']) {
            $select->where('s.domain like ?', '%' . $search['domain'] . '%'); 
        }  
        
        if ($search['status'] != '') {
            $select->where('mq.status = ?', $search['status']); 
        }  
        
        if ($search['list_id']) {
            $select->where('mq.list_id = ?', $search['list_id']); 
        } 
                
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
}


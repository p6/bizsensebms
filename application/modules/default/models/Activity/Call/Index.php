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

/* @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

Class Core_Model_Activity_Call_Index extends Core_Model_Index_Abstract
{
    /**
     * @return Zend_Paginator object
     */
    public function getPaginator()
    {
        $table = $this->_model->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $search = $this->_search;
        if(!(empty($search['start_date_from'])) and !(empty($search['start_date_to']))) {
            $startFromDate = $search['start_date_from'] . 'T00:00:00';
            $startFrom= new Zend_Date($startFromDate);
            $startFromTimeStamp = $startFrom->getTimeStamp();

            $startToDate = $search['start_date_to'] . 'T23:59:59';
            $startTo= new Zend_Date($startToDate);
            $startToTimeStamp = $startTo->getTimeStamp();

            $select->where("start_date between '$startFromTimeStamp' and '$startToTimeStamp'");
        }

         if(!(empty($search['end_date_from'])) and !(empty($search['end_date_to']))) {
            $endFromDate = $search['end_date_from'] . 'T00:00:00';
            $endFrom= new Zend_Date($endFromDate);
            $endFromTimeStamp = $endFrom->getTimeStamp();

            $endToDate = $search['end_date_to'] . 'T23:59:59';
            $endTo= new Zend_Date($endToDate);
            $endToTimeStamp = $endTo->getTimeStamp();

            $select->where("end_date between '$endFromTimeStamp' and '$endToTimeStamp'");
        }
        $callStatusId = $search['call_status_id'];
        if(is_array($callStatusId)){
            $lastElement = end($callStatusId);
            reset($callStatusId);
            $callStatusIdSyn = "call_status_id =";
            $multipleCallStatus = "";
            foreach($callStatusId as $key=>$value){
                $multipleCallStatus = $multipleCallStatus . $callStatusIdSyn. $value;
                if($value != $lastElement){
                    $multipleCallStatus = $multipleCallStatus . " OR ";
                }
            }
        $select->where($multipleCallStatus);
        }
        
#        $select->where('call_status_id like ?', '%' . $search['call_status_id'] . '%');
        if ($search['assigned_to']) {
            $select->where('call.assigned_to = ?',$search['assigned_to']);
        }
        $select->where('name like ?', '%' . $search['name'] . '%');

        $sort = $this->_sort;
        if (isset($sort)) {
            switch ($sort) {
                case 'nameAsc':
                    $select->order('call.name ASC');
                break;

                case 'nameDes':
                    $select->order('call.name DESC');
                break;

                case 'start_dateAsc':
                    $select->order('call.start_date ASC');
                break;

                case 'start_dateDes':
                    $select->order('call.start_date DESC');
                break;
            }
        } else {
            $select->order('call.created DESC');
        }
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
}

<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
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
            $startFrom= new Zend_Date($startFromDate, 'yyyy.MM.dd');
            $startFromTimeStamp = $startFrom->getTimeStamp();

            $startToDate = $search['start_date_to'] . 'T23:59:59';
            $startTo= new Zend_Date($startToDate, 'yyyy.MM.dd');
            $startToTimeStamp = $startTo->getTimeStamp();

            $select->where("start_date between '$startFromTimeStamp' and '$startToTimeStamp'");
        }

         if(!(empty($search['end_date_from'])) and !(empty($search['end_date_to']))) {
            $endFromDate = $search['end_date_from'] . 'T00:00:00';
            $endFrom= new Zend_Date($endFromDate, 'yyyy.MM.dd');
            $endFromTimeStamp = $endFrom->getTimeStamp();

            $endToDate = $search['end_date_to'] . 'T23:59:59';
            $endTo= new Zend_Date($endToDate, 'yyyy.MM.dd');
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

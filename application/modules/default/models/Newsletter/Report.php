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

class Core_Model_Newsletter_Report extends Core_Model_Abstract
{
    /**
     * @see Core_Model_Abstract::_dbTableClass
     */
    pr
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
            $startFrom= new Zend_Date($startFromDate, 'yyyy.MM.dd');
            $startFromTimeStamp = $startFrom->getTimeStamp();

            if($data['end_time']) {
                $startToDate = $data['date_to'] . $data['end_time'];
            }
            else {
                $startToDate = $data['date_to'] . 'T23:59:59';
            }
            $startTo= new Zend_Date($startToDate, 'yyyy.MM.dd');
            $startToTimeStamp = $startTo->getTimeStamp();

            $select->where("mq.sent_time between '$startFromTimeStamp' and '$startToTimeStamp'");
        }
        
        if ($data['domain']) {
            $select->where('s.domain like ?', '%' . $data['domain'] . '%'); 
        }  
        
        if ($data['status'] != '') {
            $select->where('mq.status = ?', $data['status']); 
        }  
        
        $result = $table->fetchAll($select);
        if ($result) {
           $result = $result->toArray();
        } 
        return $result;
     }
}

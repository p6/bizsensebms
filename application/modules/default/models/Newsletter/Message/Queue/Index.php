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


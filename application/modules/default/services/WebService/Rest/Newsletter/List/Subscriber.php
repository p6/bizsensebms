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

class Core_Service_WebService_Rest_Newsletter_List_Subscriber
{
    /**
     * @var object Core_Model_Newsletter_List_Subscriber
     */
    protected $_model;

    /**
     * @return object Core_Model_Newsletter_List_Subscriber
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = new Core_Model_Newsletter_List_Subscriber;
        }
        return $this->_model;
    }
    
    /**
     * @return array collection
     */
    public function fetch($listSubscriberId)
    {
        $this->getModel()->setListSubscriberId($listSubscriberId);
        return $this->getModel()->fetch();
    }


    /**
     * @return array collection of lists
     */
    public function fetchAll()
    {
        return $this->getModel()->fetchAll();
    }
  
    /**
     * @param array $data
     * @return array collection
     */
    public function create($data)
    {
      return $this->getModel()->create($data['list_id'], $data['subscriber_id']);
    }

    /**
     * @param array $data
     * @return array collection
     */
    public function edit($email, $data)
    {
        $listId = $this->getModel()->getModel()->getlistId();
        $this->getModel()->editByListIdAndEmail($listId, $email, $data);
        return $this->fetch($data['email']);
    }
    
    /** 
     * Delete a row in the list subscriber table
     */
    public function delete($listSubscriberId)
    {
        $this->getModel()->setListSubscriberId($listSubscriberId);
        $result = $this->getModel()->delete();
        return $result;
    }
}

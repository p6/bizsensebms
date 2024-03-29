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

class SavedsearchController extends Zend_Controller_Action 
{
    protected $_model;

    public function init()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_model = new Core_Model_SavedSearch();

    }
    

    public function deleteAction()
    {
        $savedSearchId = $this->_getParam('saved_search_id');
        $this->_model->setSavedSearchId($savedSearchId);
        $searchType = $this->_model->getType();
        $controller = $this->_model->getNameByType($searchType);
        $this->_model->delete();
        $this->_helper
                ->flashMessenger(
                    'The saved search item has been successfully deleted');
        $this->_helper->redirector('index', $controller, 'default');
    }

    public function createAction()
    {
        $post = $this->getRequest()->getPost();
        $searchName = $this->_getParam('name');
        $type = $this->_getParam('type');
        $savedSearchModel = new Core_Model_SavedSearch;

        $savedSearch = $savedSearchModel->create($searchName, $post, 
                            $type); 
        $url = '';
        foreach ($post as $key=>$value) {
            if(is_array($value)) {
                for($i = 0; $i < count($value); $i++) {
                    $url .= "$key";
                    $url .= "[]=" . urlencode($value[$i]) . "&" ;
                }
            }else {
                $url .= urlencode($key) . "=" . urlencode($value) . "&";
            }
        }
        
        $url = htmlentities($url);
        $controller = $this->_model->getNameByType($type);
        $targetUrl = $this->_helper->url('index', $controller, 'default') . "/?";
        $targetUrl .= $url;
        $this->_helper->json(array('target_url' => $targetUrl));
        $this->_helper->redirector('index', $controller, 'default');
    }


} 

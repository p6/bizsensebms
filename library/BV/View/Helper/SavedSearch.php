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
class BV_View_Helper_SavedSearch extends Zend_View_Helper_Url
{
    public function savedSearch()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $action = $request->getActionName();
        $controller = $request->getControllerName();
        $module = $request->getModuleName();
        $content = null;
        $url = null;
        $savedSearchModel = new Core_Model_SavedSearch; 
        $user = $this->view->currentUser;
    
        if ($action == 'index' and $controller == 'lead' and 
                $module == 'default') {
            $type = Core_Model_SavedSearch::TYPE_LEAD;
            $baseUrl = $this->url(array(
                            'module' => 'default',
                            'controller' => 'lead',
                            'action' => 'index'
                            ), NULL, true
                        ) . "?";

        } else if ($action == 'index' and 
            $controller == 'opportunity' and 
            $module == 'default') {
            $type = Core_Model_SavedSearch::TYPE_OPPORTUNITY;
            $baseUrl = $this->url(array(
                            'module' => 'default',
                            'controller' => 'opportunity',
                            'action' => 'index'
                            ), NULL, true
                        ) . "?";
        } else if ( $action == 'index' and 
            $controller == 'contact' and 
            $module == 'default') {
            $type = Core_Model_SavedSearch::TYPE_CONTACT;
            $baseUrl = $this->url(array(
                            'module' => 'default',
                            'controller' => 'contact',
                            'action' => 'index'
                            ), NULL, true
                        ) . "?";
        } else if ( $action == 'index' and 
            $controller == 'account' and 
            $module == 'default') {
            $type = Core_Model_SavedSearch::TYPE_ACCOUNT;
            $baseUrl = $this->url(array(
                            'module' => 'default',
                            'controller' => 'account',
                            'action' => 'index'
                            ), NULL, true
                        ) . "?";

        } else {
            return;
        }

        $resultCriteria = $savedSearchModel->fetchAll($type, $user);
        foreach ($resultCriteria as $row ) {
                $url = $baseUrl;
                $criteriaData = $row['s_criteria']; 
                $unserializeData = unserialize($criteriaData);

                foreach ($unserializeData as $key => $value) {
                    if(is_array($value)) {
                        for($i = 0; $i < count($value); $i++) {
                            $url .= "$key";
                            $url .= "[]=" . urlencode($value[$i]) . "&" ;
                        }
                    }else {
                        $url .= urlencode($key) . "=" . urlencode($value) . "&";
                    }
                }
                $url .= 'Submit=Search';
                $url = htmlentities($url);
                $deleteButton =  " 
                        <img class='delete_saved_search_link'  
                        src='/images/design/delete_saved_search_link.png'
                        onclick='handleSavedSearchDelete(" . $row['saved_search_id'] . ")'
                        >";
                
                $link = "<a href=" . "$url" . ">" . $row['name'] ."</a> " 
                   . "$deleteButton";
                $content .= $link."<br/>";
            }
        return $content;
    }
}

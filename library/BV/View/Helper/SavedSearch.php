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
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 */

/**
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

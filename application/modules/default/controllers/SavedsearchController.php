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
 * You can contact Binary Vibes Information Technologies Pvt. 
 * Ltd. by sending an electronic mail to info@binaryvibes.co.in
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

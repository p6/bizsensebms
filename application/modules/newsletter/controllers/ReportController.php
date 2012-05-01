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
class NewsLetter_ReportController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_List
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_Newsletter_Message_Queue;
    }

    /**
     * index page
     */ 
    public function queuereportAction() 
    {
        $form = new Core_Form_Newsletter_Report_Report;
        $form->populate($_GET);
        $this->view->form = $form;
        
        $paginator = $this->_model->getReportPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
            
        /**
         * Notify the view if the submit was hit on the search form
         */
        if ($form->getValue('submit') == 'Search') {
            $this->view->wasSearched = true;
        }
        
    }
    
    /**
     * 
     */ 
    public function queuedomainsAction() 
    {
        $this->_helper->layout->setLayout('layout_reports');
        $form = new Core_Form_Newsletter_Report_Graph;
        $form->populate($_GET);
        $this->view->form = $form;
                   
        $paginator = $this->_model->getQueueDomainPaginator($form->getValues(), $this->_getParam('sort'));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
                   
        $reportModel = new Core_Model_Newsletter_Report;
        $this->view->report = $reportModel->getGraphReport($form->getValues());
           
        /**
         * Notify the view if the submit was hit on the search form
         */
        if ($form->getValue('submit') == 'Search') {
            $this->view->wasSearched = true;
        }
        
    }
}

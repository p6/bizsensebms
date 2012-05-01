<?php 
/*
 * BizSense opportunity report controller
 *
 *
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Reports_QuoteController extends Zend_Controller_Action 
{ 
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Quote;
    }
    
    /*
     * Links to opportunity reports
     */
    public function indexAction() 
    {

    }
    
    /*
     * daterange reports
     */
    public function daterangeAction() 
    {
       $form = new Core_Form_Quote_Report_DateRange;
       $form->populate($_GET);
       $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
       
       $paginator = $this->_model->getDateRangePaginator($form->getValues(), 
                                                    $this->_getParam('sort'));
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
    }
    
    /*
     * status reports
     */
    public function statusAction() 
    {
       $form = new Core_Form_Quote_Report_Status;
       $form->populate($_GET);
       $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
       
       $paginator = $this->_model->getStatusPaginator($form->getValues(), 
                                                    $this->_getParam('sort'));
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
    }
    
    /*
     * contact/account reports
     */
    public function contactaccountAction() 
    {
       $form = new Core_Form_Quote_Report_ContactAccount;
       $form->populate($_GET);
       $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
       
       $paginator = $this->_model->getContactAccountPaginator($form->getValues(), 
                                                    $this->_getParam('sort'));
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
    }
    
    /*
     * assign to reports
     */
    public function assigntoAction() 
    {
       $form = new Core_Form_Quote_Report_AssignTo;
       $form->populate($_GET);
       $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
       
       $paginator = $this->_model->getAssignToPaginator($form->getValues(), 
                                                    $this->_getParam('sort'));
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
    }
    
}

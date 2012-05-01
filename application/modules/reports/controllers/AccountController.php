<?php 
/*
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
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/**
 * BizSense account report controller
 */
class Reports_AccountController extends Zend_Controller_Action 
{

    public $db;

    function init()
    {
        $this->db = Zend_Registry::get('db');
    }	

    /*
     * Links to account reports
     */
    public function indexAction() 
    {

    }


    /*
     * Account reports based on date range
     */
    public function daterangeAction()
    {
        $form = new Core_Form_Account_Report_Type1;

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $accountReport = new Core_Model_Account_Report;
                $accountReportUserInput = $form->getValues();
                $select = $accountReport->generateSelectObject($accountReportUserInput); 
                $accountReportDateRange = new Zend_Session_Namespace('accountReportDateRange');
               
                /*
                 * The browse action determines the search criteria 
                 * based on the reportId
                 * Thus set the reportId using $count
                 */
                $count = 0;
                if (is_array($accountReportDateRange->data)) {
                    $count = count($accountReportDateRange->data);
                }
                $count++;
                $accountReportDateRange->data["{$count}"] = $accountReportUserInput; 
                $accountReportDateRange->userInput = $accountReportUserInput; 
                $this->view->reportId = $count;

                /*
                 * Clone the $select object 
                 * The cloned objects add further where clauses
                 */                
                $selectForAccount = clone $select;

                $totalSelect = "SELECT count(1) from ( " . $selectForAccount->__toString() . " ) totalRecords";
                $count = $this->db->fetchOne($totalSelect);
                $this->view->totalAccounts = $count;
        
            } else {
               $form->populate($_POST);

            }

        } else {

           $this->view->form = $form;
        } 
 
    }

    /*
     * Browse accounts online using the select object generated in 
     * Date range action before
     */
    public function browseAction()
    {
        $accountReportDateRange = new Zend_Session_Namespace('accountReportDateRange');

        $reportId = trim($this->_getParam('reportId'));
        
        $userInput = $accountReportDateRange->data["{$reportId}"];

        $accountReport = new Core_Model_Account_Report;
        $select = $accountReport->generateSelectObject($userInput); 

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
        $this->view->reportId = $reportId;

    }

    public function csvexportAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $accountReportDateRange = new Zend_Session_Namespace('accountReportDateRange');
        $reportId = trim($this->_getParam('reportId'));
        $userInput = $accountReportDateRange->data["{$reportId}"];
       
        $accountReport = new Core_Model_Account_Report;
        $select = $accountReport->generateSelectObject($userInput); 
        
        $result = $this->db->fetchAll($select, array(), Zend_Db::FETCH_ASSOC);
        
        $file = '';
        $file .= "Account Id, Account Name, Phone, Mobile, Website, Fax, Email,";
        $file .= "billing_address_line_1, billing_address_line_2, billing_address_line_3,";
        $file .= "billing_address_line_4, Billing City, Billing State, ";
        $file .= "Billing Postal Code, Billing Country, shipping_address_line_1,";
        $file .= "shipping_address_line_2, shipping_address_line_3, shipping_address_line_4,";
        $file .= "Shipping City, Shipping State, Shipping Postal Code, Shipping Country,";
        $file .= "Description, Assigned To, Created By, Created, Branch Id, updated,";
        $file .= "ledger_id, campaign_id, tin, pan, Service Tax Number,";
        $file .= "Assigned To Email, Branch Name";
        $file .= PHP_EOL;
        foreach ($result as $row) {
            $line = '';
            foreach ($row as $item) {
                $escapeSpecialChar = str_replace("\r\n", "", $item);
                $escapeSpecialChar = str_replace(",", " ", $escapeSpecialChar);
                $escapeSpecialChar = str_replace('"', '""', $escapeSpecialChar);
                $escapeSpecialChar = '"' . $escapeSpecialChar . '",';
               $line .= $escapeSpecialChar;
            }
            $file .= $line . PHP_EOL;
        } 
    
#        echo $file;
        $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 'attachment; filename=accountreport.csv')
                            ->appendBody($file);
    }
} 

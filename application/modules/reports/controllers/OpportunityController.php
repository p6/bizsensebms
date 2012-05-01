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

class Reports_OpportunityController extends Zend_Controller_Action 
{

    public $db;

    function init()
    {
        $this->db = Zend_Registry::get('db');
    }	

    /*
     * Links to opportunity reports
     */
    public function indexAction() 
    {

    }


    /*
     * Opportunity reports based on date range
     */
    public function daterangeAction()
    {
        $this->_helper->layout->setLayout('layout_reports');
        $form = new Core_Form_Opportunity_Report_Type1;

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $opportunityReport = new Core_Model_Opportunity_Report;
                $opportunityReportUserInput = $form->getValues();
                $select = $opportunityReport->generateSelectObject($opportunityReportUserInput); 
                $opportunityReportDateRange = new Zend_Session_Namespace('opportunityReportDateRange');
               
                /*
                 * The browse action determines the search criteria 
                 * based on the reportId
                 * Thus set the reportId using $count
                 */
                $count = 0;
                if (is_array($opportunityReportDateRange->data)) {
                    $count = count($opportunityReportDateRange->data);
                }
                $count++;
                $opportunityReportDateRange->data["{$count}"] = $opportunityReportUserInput; 
                $opportunityReportDateRange->userInput = $opportunityReportUserInput; 
                $this->view->reportId = $count;

                /*
                 * Clone the $select object 
                 * The cloned objects add further where clauses
                 */                
                $selectForOpportunityStatus = clone $select;
                            
                $leadHelper = new Core_Model_Lead_Helper;
                $leadSource = $leadHelper->getLeadSource();
                
                /* 
                 * Generate the report array based on opportunity source
                 */
                $opportunitySourceReportSummary = array();
                foreach ($leadSource as $ls){
                    $lsNameSelect = clone $select;
                    $lsName  = $lsNameSelect->where('ls.name = ? ', $ls->name);
                    $totalSelect = "SELECT count(1) from ( " . $lsName->__toString() . " ) totalRecords";
#                    echo $totalSelect; exit(1);
                    $count = $this->db->fetchOne($totalSelect);
                    $newArrayKeyToBeInserted = $ls->name;
                    $opportunitySourceReportSummary["{$newArrayKeyToBeInserted}"] = $count;
                } 
                 
                $this->view->opportunitySourceReportSummary = $opportunitySourceReportSummary;

                /* Generate the report array based on opportunity status
                 *
                 */
                $opportunityHelper = new Core_Model_Opportunity_Helper;
                $opportunityStatus = $opportunityHelper->getOpportunityStatus();
                $opportunityStatusReportSummary = array();
                foreach ($opportunityStatus as $ls){
                    $lsNameSelect = clone $select;
                    $lsName  = $lsNameSelect->where('ss.name = ? ', $ls->name);
                    $totalSelect = "SELECT count(1) from ( " . $lsName->__toString() . " ) totalRecords";
                    $count = $this->db->fetchOne($totalSelect);
                    $newArrayKeyToBeInserted = $ls->name;
                    $opportunityStatusReportSummary["{$newArrayKeyToBeInserted}"] = $count;
                } 
                 
                $this->view->opportunityStatusReportSummary = $opportunityStatusReportSummary;

        
            } else {
               $form->populate($_POST);

#               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        } 
 
  #        $this->view->form = $form;
    }

    /*
     * Browse opportunitys online using the select object generated in 
     * Date range action before
     */
    public function browseAction()
    {
        $opportunityReportDateRange = new Zend_Session_Namespace('opportunityReportDateRange');

        $reportId = $this->_getParam('reportId');
        $userInput = $opportunityReportDateRange->data["{$reportId}"];

        $opportunityReport = new Core_Model_Opportunity_Report;
        $select = $opportunityReport->generateSelectObject($userInput); 

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

        $opportunityReportDateRange = new Zend_Session_Namespace('opportunityReportDateRange');
        $reportId = trim($this->_getParam('reportId'));
        $userInput = $opportunityReportDateRange->data["{$reportId}"];

        $opportunityReport = new Core_Model_Opportunity_Report;
        $select = $opportunityReport->generateSelectObject($userInput); 
        
        $result = $this->db->fetchAll($select, array(), Zend_Db::FETCH_ASSOC);

        $file = '';
        $file .= "Name, Amount, Expected Close Date, Created, Last Updated,";
        $file .= "Source, Stage, Account Name, Email, Branch Name, First Name";
        $file .= "Middle Name, Last Name";
        $file .= PHP_EOL;
        $columnsToDisplay = array('name', 'amount', 'expectedCloseDate', 'created', 'lastUpdated', 'source', 
            'stage', 'accountName', 'email', 'branchName', 'firstName', 'middleName', 'lastName');
        foreach ($result as $row) {
            $line = '';
            foreach ($row as $key=>$item) {
               if (in_array($key, $columnsToDisplay)) { 
                $escapeSpecialChar = str_replace("\r\n", "", $item);
                $escapeSpecialChar = str_replace(",", " ", $escapeSpecialChar);                    
                $escapeSpecialChar = str_replace('"', '""', $escapeSpecialChar);
                $escapeSpecialChar = '"' . $escapeSpecialChar . '",';
                $line .= $escapeSpecialChar;
               } 
            }
            $file .= $line . PHP_EOL;
        } 
    
#        echo $file;
        $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 'attachment; filename=opportunityreport.csv')
                            ->appendBody($file);
    }
} 

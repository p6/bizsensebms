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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Reports_LeadController extends Zend_Controller_Action 
{

    public $db;

    function init()
    {
        $this->db = Zend_Registry::get('db');
    }	

    /**
     * Links to lead reports
     */
    public function indexAction() 
    {
        $form = new Core_Form_Lead_Report;

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                /* 
                 * Process data
                 */
                $select = $this->db->select();
                $select->from(array('l' => 'lead'),
                            array('leadId', 'firstName', 'lastName', 'companyName', 'mobile'))
                        ->joinLeft(array('ls'=>'leadSource'),
                            'l.leadSourceId = ls.leadSourceId', array('ls.name'=>'name'))
                        ->joinLeft(array('lst'=>'leadStatus'),
                            'l.leadStatusId = lst.leadStatusId', array('lst.name'=>'name'))
                        ->group('l.leadId');
                $name = $this->_getParam('name');
                if (isset($name)) {
                    $select->where('firstName like ?', '%' . $this->_getParam('name') . '%');
                    $select->orWhere('middleName like ?', '%' . $this->_getParam('name') . '%');
                    $select->orWhere('lastName like ?', '%' . $this->_getParam('name') . '%');
                } else {
                    $select->where('firstName like ?', '%' . $this->_getParam('firstName') . '%');
                    $select->where('middleName like ?', '%' . $this->_getParam('middleName') . '%');
                    $select->where('lastName like ?', '%' . $this->_getParam('lastName') . '%');
                }                
                $sql = $select->__toString();
                $totalSelect = $this->db->select()->from(
                    array(
                        'tmp' => new Zend_Db_Expr('(' . $sql . ')'), array('count(1)') 
                    )
                );
                $totalSelect = "SELECT count(1) from ($sql) totalRecords";
               
                $totalResult = $this->db->fetchOne($totalSelect);
             echo "Total is $totalResult";    
                $result = $this->db->fetchAll($select);
                $this->view->result = $result;
                
                $this->view->reportGenerated = true;
            } else {
               $form->populate($_POST);

               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        } 
    }


    /*
     * Lead reports based on date range
     */
    public function daterangeAction()
    {
        $this->_helper->layout->setLayout('layout_reports');
        $form = new Core_Form_Lead_Report_Type1;

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $leadReport = new Core_Model_Lead_Report;
                $leadReportUserInput = $form->getValues();
                $select = $leadReport->generateSelectObject($leadReportUserInput); 
                $leadReportDateRange = new Zend_Session_Namespace('leadReportDateRange');
               
                /*
                 * The browse action determines the search criteria 
                 * based on the reportId
                 * Thus set the reportId using $count
                 */
                $count = 0;
                if (is_array($leadReportDateRange->data)) {
                    $count = count($leadReportDateRange->data);
                }
                $count++;
                $leadReportDateRange->data["{$count}"] = $leadReportUserInput; 
                $leadReportDateRange->userInput = $leadReportUserInput; 
                $this->view->reportId = $count;

                /*
                 * Clone the $select object 
                 * The cloned objects add further where clauses
                 */                
                $selectForLeadStatus = clone $select;
                            
                $leadHelper = new Core_Model_Lead_Helper;
                $leadSource = $leadHelper->getLeadSource();
                
                /* 
                 * Generate the report array based on lead source
                 */
                $leadSourceReportSummary = array();
                foreach ($leadSource as $ls){
                    $lsNameSelect = clone $select;
                    $lsName  = $lsNameSelect->where('ls.name = ? ', $ls->name);
                    $totalSelect = "SELECT count(1) from ( " . $lsName->__toString() . " ) totalRecords";
#                    echo $totalSelect; exit(1);
                    $count = $this->db->fetchOne($totalSelect);
                    $newArrayKeyToBeInserted = $ls->name;
                    $leadSourceReportSummary["{$newArrayKeyToBeInserted}"] = $count;
                } 
                 
                $this->view->leadSourceReportSummary = $leadSourceReportSummary;

                /* Generate the report array based on lead status
                 *
                 */
                $leadStatus = $leadHelper->getLeadStatus();
                $leadStatusReportSummary = array();
                foreach ($leadStatus as $ls){
                    $lsNameSelect = clone $select;
                    $lsName  = $lsNameSelect->where('lst.name = ? ', $ls->name);
                    $totalSelect = "SELECT count(1) from ( " . $lsName->__toString() . " ) totalRecords";
                    $count = $this->db->fetchOne($totalSelect);
                    $newArrayKeyToBeInserted = $ls->name;
                    $leadStatusReportSummary["{$newArrayKeyToBeInserted}"] = $count;
                } 
                 
                $this->view->leadStatusReportSummary = $leadStatusReportSummary;

                /* Generate the report array based on conversion status
                 *
                 */
                $convertSelect = clone $select;
                $convertQuery = $convertSelect->where('l.converted = 1');
                $totalSelect = "SELECT count(1) FROM ( " . $convertQuery->__toString() . " ) totalRecords";
                $count = $this->db->fetchOne($totalSelect);
                $leadConvertedReportSummary['Converted'] = $count;
     
                $convertSelect = clone $select;
                $convertQuery = $convertSelect->where('l.converted = 0');
                $totalSelect = "SELECT count(1) FROM ( " . $convertQuery->__toString() . " ) totalRecords";
                $count = $this->db->fetchOne($totalSelect);
                $leadConvertedReportSummary['Not Converted'] = $count;
 
                $this->view->leadConvertedReportSummary = $leadConvertedReportSummary;
        
            } else {
               $form->populate($_POST);

#               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        } 
 
  #        $this->view->form = $form;
    }

    /**
     * Browse leads online using the select object generated in 
     * Date range action before
     */
    public function browseAction()
    {
        $leadReportDateRange = new Zend_Session_Namespace('leadReportDateRange');

        $reportId = $this->_getParam('reportId');
        if (!is_numeric($reportId)) {
            return;
        }
        $userInput = $leadReportDateRange->data["{$reportId}"];
        
        $leadReport = new Core_Model_Lead_Report;
        $select = $leadReport->generateSelectObject($userInput); 

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

        $leadReportDateRange = new Zend_Session_Namespace('leadReportDateRange');
        $reportId = trim($this->_getParam('reportId'));
     
        $userInput = $leadReportDateRange->data["{$reportId}"];
        
        $leadReport = new Core_Model_Lead_Report;
        $select = $leadReport->generateSelectObject(); 
        
        $result = $this->db->fetchAll($select, array(), Zend_Db::FETCH_ASSOC);
        
        $file = '';
        $file .= "Sl. no., First Name, Middele Name, Last Name, Created,";
        $file .= "Updated, Source, Status, Assign To,Branch";
        $file .= PHP_EOL;
        
        foreach ($result as $row) {
            $line = '';
            $date = new Zend_Date();
            $date->setTimestamp($row['created']);
            $row['created'] = $date->toString();  
            
            if ($row['updated']) {
                $date->setTimestamp($row['updated']);
                $row['updated'] = $date->toString();
            }
            foreach ($row as $item) {
                $escapeSpecialChar = str_replace("\r\n", "", $item);
                $escapeSpecialChar = str_replace(",", " ", $escapeSpecialChar);
                $escapeSpecialChar = str_replace('"', '""', $escapeSpecialChar);
                $escapeSpecialChar = '"' . $escapeSpecialChar . '",';
               $line .= $escapeSpecialChar;
            }
            $file .= $line . PHP_EOL;
        } 
        
        $this->getResponse()->setHeader('Content-Type', 'application/csv')
                            ->setHeader('Content-disposition', 'attachment; filename=leadreport.csv')
                            ->appendBody($file);
    }
} 

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

class Reports_ContactController extends Zend_Controller_Action 
{

    public $db;

    function init()
    {
        $this->db = Zend_Registry::get('db');
    }	

    /*
     * Links to contact reports
     */
    public function indexAction() 
    {

    }


    /*
     * Contact reports based on date range
     */
    public function daterangeAction()
    {
        $form = new Core_Form_Contact_Report_Type1;

        if ($this->_request->isPost()) {

            if ($form->isValid($_POST)) {
                $contactReport = new Core_Model_Contact_Report;
                $contactReportUserInput = $form->getValues();
                $select = $contactReport->generateSelectObject($contactReportUserInput); 
                $contactReportDateRange = new Zend_Session_Namespace('contactReportDateRange');
               
                /*
                 * The browse action determines the search criteria 
                 * based on the reportId
                 * Thus set the reportId using $count
                 */
                $count = 0;
                if (is_array($contactReportDateRange->data)) {
                    $count = count($contactReportDateRange->data);
                }
                $count++;
                $contactReportDateRange->data["{$count}"] = $contactReportUserInput; 
                $contactReportDateRange->userInput = $contactReportUserInput; 
                $this->view->reportId = $count;

                /*
                 * Clone the $select object 
                 * The cloned objects add further where clauses
                 */                
                $selectForContact = clone $select;
       #         $result = $this->db->fetchAll($selectForContact);

                $totalSelect = "SELECT count(1) from ( " . $selectForContact->__toString() . " ) totalRecords";
                $count = $this->db->fetchOne($totalSelect);
                $this->view->totalContacts = $count;
        
            } else {
               $form->populate($_POST);

#               $this->view->form = $form;
            }

        } else {

           $this->view->form = $form;
        } 
 
    }

    /*
     * Browse contacts online using the select object generated in 
     * Date range action before
     */
    public function browseAction()
    {
        $contactReportDateRange = new Zend_Session_Namespace('contactReportDateRange');

        $reportId = $this->_getParam('reportId');
        
        $userInput = $contactReportDateRange->data["{$reportId}"];
        
        $contactReport = new Core_Model_Contact_Report;
        $select = $contactReport->generateSelectObject($userInput); 

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

        $contactReportDateRange = new Zend_Session_Namespace('contactReportDateRange');
        $reportId = trim($this->_getParam('reportId'));
        
        $userInput = $contactReportDateRange->data["{$reportId}"];

        $contactReport = new Core_Model_Contact_Report;
        $select = $contactReport->generateSelectObject($userInput); 
        
        $result = $this->db->fetchAll($select, array(), Zend_Db::FETCH_ASSOC);
        
        $file = '';
        $file .= "Contact Id, First Name, Middle Name, Last Name, Work Phone,";
        $file .= "Home Phone, Mobile, Fax, Title, Department, Work Email, ";
        $file .= "Other Email, Do Not Call, Email Opt Out, Billing City, ";
        $file .= "Billing State, Billing Postal Code, Billing Country, ";
        $file .= "Shipping City, Shipping State, Shipping Postal Code,";
        $file .= "Shipping Country, Description, Reports To, Salutation Id,";
        $file .= "Assistant Id, Birthday, Birthday Date, Birthday Month,";
        $file .= "Assigned_To, Created By, Created, Updated, Branch Id,";
        $file .= "Account Id, billing_address_line_1, billing_address_line_2,";
        $file .= "billing_address_line_3, billing_address_line_4, shipping_address_line_1,";
        $file .= "shipping_address_line_2, shipping_address_line_3, shipping_address_line_4,";
        $file .= "ss_enabled, ss_active, ss_password, ledger_id, campaign_id,";
        $file .= "Account Name, Assigned To Email, Branch Name";
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
                            ->setHeader('Content-disposition', 'attachment; filename=contactreport.csv')
                            ->appendBody($file);
    }
    
    /*
     * Links to contact reports
     */
    public function selfserviceAction() 
    {
       $form = new Core_Form_Contact_Report_SelfService;
       $form->populate($_POST);
       $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
       $contactModel = new Core_Model_Contact;
       $formValues = $form->getValues();
       if ($formValues['self_service'] != null) {
          $this->view->value = $formValues['self_service'];
       }
       else {
          $this->view->value = 1;
       } 
       
       $paginator = $contactModel->getSelfservicePaginator($form->getValues(), 
                                                    $this->_getParam('sort'));
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;
    }
    
    /*
     * multiple enable or disable
     */
    public function multipleenableAction() 
    {
       if ($_POST['option'] == 1) {

          for ($i = 0; $i < count($_POST['select']); $i++ ) {
            $contactModel = new Core_Model_Contact($_POST['select'][$i]);
            $contactModel->enableSelfService();
          }
       }
       else {
          for ($i = 0; $i < count($_POST['select']); $i++ ) {
            $contactModel = new Core_Model_Contact($_POST['select'][$i]);
            $contactModel->disableSelfService();
          }
       }
       $this->_helper->redirector('selfservice','contact', 'reports');
    }

} 

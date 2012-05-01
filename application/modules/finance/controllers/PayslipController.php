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

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Finance_PayslipController extends Zend_Controller_Action 
{

    /**
     * @var object Core_Model_Finance_Payslip
     */
    protected $_model;

    /**
     * @var object Core_Model_Finance_PayslipField
     */
    protected $_modelPayslipField;
    
    /**
     * Initialize the controller
     */
    function init()
    {
        $this->_modelPayslipField = new Core_Model_Finance_PayslipField;
        $this->_model = new Core_Model_Finance_Payslip;
    }

    /**
     * Browsable, sortable, searchable list of Finance Payslip
     */
    public function indexAction()
    {
       $form = new Core_Form_Finance_Payslip_Search;
       $form->populate($_GET);
       $this->view->form = $form;
      
       if ($form->getValue('submit') == 'search') {
            $this->view->wasSearched = true;
        }
        
       $paginator = $this->_model->getPaginator($form->getValues(), 
                                                    $this->_getParam('sort'));  
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(25);
       $this->view->paginator = $paginator;   
    }
    
    public function settingsAction()
    {
        $form = new Core_Form_Finance_Payslip_Settings;
        $form->setAction(
            $this->_helper->url(
                'settings',
                'payslip',
                'finance'
            )
        );
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_modelPayslipField->settings($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                              'The payslip settings was created successfully');
                $this->_helper->Redirector('index');
            }
        }
        else {
            $enabledFeilds =  $this->_modelPayslipField->getEnabledFields();
            $ledgers =  $this->_modelPayslipField->getLedger();
            $defaultValues = array_merge($enabledFeilds, $ledgers);
            $form->populate($defaultValues);
        }
    }
    
    public function createAction()
    {
        $form = new Core_Form_Finance_Payslip_Create;
        $form->setAction(
            $this->_helper->url(
                'create',
                'payslip',
                'finance'
            )
        );
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->create($this->getRequest()->getPost());
                $this->_helper->FlashMessenger(
                              'The payslip was created successfully');
                $this->_helper->Redirector('index');
            }
        }
    }
    
    /**
     * View the details of the payslip
     */
    public function viewdetailsAction()
    {
        $payslipId = $this->_getParam('payslip_id');
        $payslipModel = new Core_Model_Finance_Payslip($payslipId);
        $this->view->payslipId = $payslipId;
        $this->view->payslipRecord = $payslipModel->fetch();
        $this->view->payslipItems = $payslipModel->getItemsToDisplay();
    }
   
    /**
     *
     */
    public function exportAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $payslipId = $this->_getParam('payslip_id');
        $payslipModel = new Core_Model_Finance_Payslip($payslipId);
        $payslipRecord = $payslipModel->fetch();
        $payslipItems = $payslipModel->getItemsToDisplay();
       
        $pdf = new Core_Model_Finance_Payslip_Pdf_Create();
        $pdf->setSummaryDetails($payslipId,$payslipRecord, $payslipItems);
        $pdf->run();
        $pdfPath = APPLICATION_PATH . '/data/documents/reports/pay_slip_summary' . '.pdf';
        $pdf->Output($pdfPath, 'F');
        $file = file_get_contents($pdfPath);
    
        ini_set('zlib.output_compression','0');
        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="pay_slip_summary.pdf"')
            ->setHeader('Content-Length', strlen($file))
            ->setHeader('Pragma: public')
            ->setHeader('Cache-Control: private, max-age=0, must-revalidate')
            ->appendBody($file);  
    }

     /**
     * Edit a Vendor Details
     */
    public function editAction()
    {
        $payslipId = $this->_getParam('payslip_id'); 
        $this->_model->setpayslipId($payslipId);
  
        $form = new Core_Form_Finance_Payslip_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'payslip', 
                'finance',
                array(
                    'payslip_id'=>$payslipId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger(
                                'The payslip has been edited successfully');
                $this->_helper->redirector('index', 'payslip', 'finance',
                    array('payslip_id'=>$payslipId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        $payslipRecord =  $this->_model->fetch();
        $date = new Zend_Date();
        $date->setTimestamp($payslipRecord['date']);
        $payslipRecord['date'] = 
                          $this->view->timestampToDojo($payslipRecord['date']); 
        //$payslipRecord['ledger_id'] = $payslipRecord['indirect_expense_ledger_id'];
        $itemsRecord =  $this->_model->getItems();
        $defaultValues = array_merge($payslipRecord, $itemsRecord);
        $form->populate($defaultValues);
    }
    
    /**
     * Delete the payslip
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setPayslipId($this->_getParam('payslip_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The payslip was successfully deleted'; 
        } else {
           $message = 'The payslip could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'payslip', 'finance');
    }
}

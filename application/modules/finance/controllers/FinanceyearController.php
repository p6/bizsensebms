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

class Finance_FinanceyearController extends Zend_Controller_Action 
{
    /**
     * Currency 
     */
    public function indexAction()
    {
        $form = new Core_Form_Finance_FinanceYear_Create();
        $form->setAction($this->_helper->url(
                'index', 
                'financeyear', 
                'finance'
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $variableModel = new Core_Model_Variable;
                $data = $form->getValues();
                $startDate = new Zend_Date($data['finance_year_start_date']);
                $variableModel->save('finance_year_start_date',
                                        $startDate->getTimestamp());
                $endDate = new Zend_Date($data['finance_year_end_date']);
                $variableModel->save('finance_year_end_date',
                                        $endDate->getTimestamp());
                
                $this->_helper->FlashMessenger(
                             'The finance year has been changed successfully');
                $this->_helper->redirector('index', 'index', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $variableModel = new Core_Model_Variable('finance_year_start_date');
            if ($variableModel->getValue() != '') {
                $defaultValues['finance_year_start_date'] = 
                     $this->view->timestampToDojo($variableModel->getValue()); 
                $form->populate($defaultValues);
            }
            
            $variableModel = new Core_Model_Variable('finance_year_end_date');
            if ($variableModel->getValue() != '') {
                $defaultValues['finance_year_end_date'] = 
                      $this->view->timestampToDojo($variableModel->getValue()); 
                $form->populate($defaultValues);
            }
        }
        
    }
} 

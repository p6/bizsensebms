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

class Finance_CurrencyController extends Zend_Controller_Action 
{
    /**
     * Currency 
     */
    public function indexAction()
    {
        $form = new Core_Form_Finance_Currency_Create();
        $form->setAction($this->_helper->url(
                'index', 
                'currency', 
                'finance'
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $variableModel = new Core_Model_Variable;
                $data = $form->getValues();
                $variableModel->save('finance_currency_name',
                                        $data['finance_currency_name']);
                $variableModel->save('finance_currency_symbol',
                                        $data['finance_currency_symbol']);
                $variableModel->save('finance_currency_Fraction_al_Currency',
                              $data['finance_currency_Fraction_al_Currency']);
                
                $this->_helper->FlashMessenger(
                               'The Currency has been changed successfully');
                $this->_helper->redirector('index', 'index', 'finance');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
        else {
            $variableModel = new Core_Model_Variable('finance_currency_name');
            if ($variableModel->getValue() != '') {
                $currency['finance_currency_name'] = 
                                                $variableModel->getValue();
                $form->populate($currency);
            }
            
            $variableModel = new Core_Model_Variable('finance_currency_symbol');
            if ($variableModel->getValue() != '') {
                $currency['finance_currency_symbol'] = 
                                                $variableModel->getValue();
                $form->populate($currency);
            }
            
            $variableModel = new Core_Model_Variable(
                                      'finance_currency_Fraction_al_Currency');
            if ($variableModel->getValue() != '') {
                $currency['finance_currency_Fraction_al_Currency'] = 
                                                $variableModel->getValue();
                $form->populate($currency);
            }
            
            
        }
        
    }
} 

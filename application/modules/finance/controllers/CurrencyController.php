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

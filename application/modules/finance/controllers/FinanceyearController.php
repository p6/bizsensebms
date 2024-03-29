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

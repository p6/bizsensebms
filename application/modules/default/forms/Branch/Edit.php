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

class Core_Form_Branch_Edit extends Core_Form_Branch_Create
{
    protected $_branchId;
    
    /*
     * Set the branch office Id
     */ 
    public function __construct($branchId = null)
    {
        if (is_numeric($branchId)) {
            $this->_branchId = $branchId;
        }
    }
    
    /*
     * @return Zend_Form
     * Web form to edit branch office details
     */
    public function getForm()
    {
        $form = parent::getForm();
        $branch = new Core_Model_Branch($this->_branchId);
        $form->setAction('/admin/branch/edit/branchId/' . $this->_branchId);
        $form->getElement('branch_name')->removeValidator('BV_Validate_BranchCreateable')
                    ->removeValidator('Zend_Validate_Db_NoRecordExists')
                    ->addValidator(new Zend_Validate_Db_NoRecordExists('branch', 'branch_name', 
                        array('field'   =>  'branch_id', 'value' => $this->_branchId)));
            
        $branchData = $branch->fetch();
        $form->populate( (array) $branchData);
        return $form;
    }
}    

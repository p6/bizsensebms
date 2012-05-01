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

/**
 * Edit branch office form
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

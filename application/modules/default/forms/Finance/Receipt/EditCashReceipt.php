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
class Core_Form_Finance_Receipt_EditCashReceipt extends Core_Form_Finance_Receipt_Create
{
    protected $_financeReceiptModel;

    public function __construct($_financeReceiptModel)
    {
        $this->_financeReceiptModel = $_financeReceiptModel;
        parent::__construct();
    }

    /**
     * @return Zend_Form
     */
    public function init()
    {
        parent::init();
        $defaultValues = $this->_financeReceiptModel->fetch();
        $defaultValues['from_type']= $defaultValues['type'];
        if ($defaultValues['type'] == 1) {
           $defaultValues['account_id'] =  $defaultValues['type_id'];
        }
        if ($defaultValues['type'] == 2) 
        {
            $defaultValues['contact_id'] = $defaultValues['type_id'];
        }
        $defaultValues['cashaccount_id'] = $defaultValues['mode_account_id'];
        $this->populate($defaultValues);
    }
}

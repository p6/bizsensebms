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
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending 
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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. \
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Form_Finance_Receipt_Edit extends Core_Form_Finance_Receipt_CreateReceipt
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
        $receiptRecord = $this->_financeReceiptModel->fetch();
        $receiptRecord['from_type'] = $receiptRecord['type'];
        if ($receiptRecord['type'] == 1) {
           $receiptRecord['account_id'] =  $receiptRecord['type_id'];
        } 
        if ($receiptRecord['type'] == 2) {
            $receiptRecord['contact_id'] = $receiptRecord['type_id'];
        }
        $receiptRecord['bank_account_id'] = $receiptRecord['mode_account_id'];
        $receiptbankModel = new Core_Model_Finance_Receipt_Bank;
        $receiptBankRecord = 
           $receiptbankModel->fetchByReceiptId($receiptRecord['receipt_id']);
       
        $defaultValues = array_merge($receiptRecord, $receiptBankRecord);
        $this->populate($defaultValues);
    }
}

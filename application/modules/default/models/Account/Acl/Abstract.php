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

/**
 * Validate whether the account alredy exists in the database
 */
abstract class Core_Model_Account_Acl_Abstract 
{
    /**
     * @var object Core_Model_Account
     */
    protected $_account;

    /**
     * @var array account data
     */
    protected $_accountRecord;

    /**
     * @accountId int accoUnt id
     */
    protected $_accountId;

    public function __construct($value)
    {
		if(is_array($value)) {
            if (isset($value['account_id'])){
			    $accountId = $value['account_id'];
            } elseif (isset($value['id'])) {
			    $accountId = $value['id'];
            }
		} else {
            $accountId = $value;
        }
        $this->_accountId = $accountId;
		$model = new Core_Model_Account($accountId);
        $this->_account = $model;
		$this->_accountRecord = $model->fetch();

	}
}

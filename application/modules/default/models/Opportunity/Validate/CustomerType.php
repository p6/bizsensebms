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
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Opportunity_Validate_CustomerType extends Zend_Validate_Abstract
{
    const CONTACT_NOT_PROVIDED = 'contact not provided';
    const ACCOUNT_NOT_PROVIDED = 'account not provided';

    protected $_messageTemplates = array(
        self::CONTACT_NOT_PROVIDED => "Contact must be selected from Reference To Contact field",
        self::ACCOUNT_NOT_PROVIDED => "Account must be selected from Reference To Account field"
    );


    public function isValid($value, $context = null)
    {
        $this->_value = $value;

        if ($value == Core_Model_Opportunity::CUSTOMER_TYPE_ACCOUNT) {
            if (is_numeric($context['account_id'])) {
                return true;
            } else {
                $this->_error(self::ACCOUNT_NOT_PROVIDED);    
                return false;
            }
        } else {
            if (is_numeric($context['contact_id'])) {
                return true;
            } else {
                $this->_error(self::CONTACT_NOT_PROVIDED);    
                return false;
            }
        }

        return true; 
    }
}


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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Newsletter_Message_Queue_Validate_VerpFields extends Zend_Validate_Abstract
{
    const RETURN_PATH_NOT_SPECIFIED = 'return_path';
    const FROM_HEADER_NOT_SPECIFIED = 'from_header_not_specified';

    /**
     * @see Zend_Validate_Abstract::_messageTemplates
     */
    protected $_messageTemplates = array(
        self::RETURN_PATH_NOT_SPECIFIED => 'Return-Path value cannot be empty if VERP is enabled',
        self::FROM_HEADER_NOT_SPECIFIED => 'From header value cannot be empty if VERP is enabled' 
    );
   
    /**
     * @see Zend_Validate_Abstract::isValid()
     */
    public function isValid($value, $context = null)
    {
        $toReturn = true;

        $this->_setValue($value);
        if ($value != 1) {
            return true;
        }

        if ($context['newsletter_message_queue_settings_bounce_return_path'] == '') {
            $this->_error(self::RETURN_PATH_NOT_SPECIFIED);
            $toReturn = false;
        }
        if ($context['newsletter_message_queue_settings_bounce_from'] == '') {
            $this->_error(self::FROM_HEADER_NOT_SPECIFIED);
            $toReturn = false;
        }

        return $toReturn;
    }
}


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
 * @category BizSense
 * @package  Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Model_Invoice_Settings extends Core_Model_Abstract
{
    const VARIABLE_NAME_PREFIX = 'core_invoice_prefix';
    const VARIABLE_NAME_SUFFIX = 'core_invoice_suffix';

    /**
     * @var object the variable model
     */
    protected $_variableModel;
    
    /**
     * @return string the invoice prefix
     */
    public function getPrefix()
    {
        return $this->getVariableModel()
            ->setVariable(self::VARIABLE_NAME_PREFIX)
            ->getValue();
    }

    /**
     * @return string the invoice suffix
     */
    public function getSuffix()
    {
        return $this->getVariableModel()
            ->setVariable(self::VARIABLE_NAME_SUFFIX)
            ->getValue();
    }

    /**
     * Set the invoice suffix
     * @param string $suffix
     */
    public function setSuffix($suffix)
    {
        $this->getVariableModel()->save(self::VARIABLE_NAME_SUFFIX, $suffix);
        return $this;
    }

    /**
     * Set the invoice prefix
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->getVariableModel()->save(self::VARIABLE_NAME_PREFIX, $prefix);
        return $this;
    }


    public function getVariableModel()
    {
        if (!$this->_variableModel) {
            $this->_variableModel = new Core_Model_Variable();
        }
        return $this->_variableModel;
    }

}    

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
class Core_Model_Variable extends BV_Model_Essential_Abstract
{
    /**
     * The name of the variable
     */
    protected $_variable;

    /**
     * Set the variable 
     */
    public function __construct($variable = null)
    {
        if (isset($variable)) {
            $this->_variable = $variable;
        }
        parent::__construct();
    } 
 
    /**
     * Set the variable name
     * @param string $variable
     */
    public function setVariable($variable)
    {
        $this->_variable = $variable;
        return $this;
    }


    /**
     * @return the value of the variable
     */
    public function getValue()
    {
        return $this->db->fetchOne('SELECT value FROM variable WHERE name = ?' , $this->_variable);
    }     

    /**
     * Save a variable
     *
     * @param string $variable the variable name
     * @param string $value the value of the variable
     * @return void
     */
    public function save($variable, $value)
    {
        $db = Zend_Registry::get('db');
        $result = $db->fetchOne("select name from variable where name = ?", $variable);

        $data = array(
            'name' => $variable,
            'value' => $value,
        );

        if ($result) {
            
            $where[] = "name = '" . $variable  . "'";
            $db->update('variable', $data, $where);
        } else {
            $db->insert('variable', $data);
        }
    }

}

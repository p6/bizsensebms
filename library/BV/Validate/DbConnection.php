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
class BV_Validate_DbConnection extends Zend_Validate_Abstract
{
    const BV_VALIDATE_DBCONNECTION_FAIL = 'Failed';

    protected $_messageTemplates = array(
        self::BV_VALIDATE_DBCONNECTION_FAIL => 
            'Connection to the database could not be established
		    with the supplied credentials'
    );

    /**
     * @param string $value
     * @param array $context with database configuration values
     * the keys for the array should be dbDriver, dbHostname, 
     * dbName, dbUsername, dbPassword
     */
    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

	    if (is_array($context)) {
		    $dbDriver = $context['db_driver'];
		    $dbHostname = $context['db_hostname'];	
		    $dbName = $context['db_name'];
		    $dbUsername = $context['db_username'];
		    $dbPassword = $context['db_password'];
	
		    $parameters = array(
                'host' => $context['db_hostname'],
                'username' => $context['db_username'],
                'password' => $context['db_password'],
                'dbname'   => $context['db_name']
            );

            try {

                $db = Zend_Db::factory($context['db_driver'], $parameters);
                $db->getConnection();

            } catch (Zend_Db_Adapter_Exception $e) {
                /**
                 * perhaps a failed login credential, or perhaps the RDBMS is not running
                 */
		        $this->_error(self::BV_VALIDATE_DBCONNECTION_FAIL);
	            return false;

            } catch (Zend_Exception $e) {
                /* 
                 * perhaps factory() failed to load the specified Adapter class
                 */
		        $this->_error(self::BV_VALIDATE_DBCONNECTION_FAIL);
	            return false;
            }
	
	    } 
      
        return true;
    }

}

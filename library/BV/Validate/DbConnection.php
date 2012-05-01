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

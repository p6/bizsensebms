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

abstract class Core_Service_CliAbstract
{
    const SERVICE_MODE_WEB = 'web';
    const SERVICE_MODEL_CLI = 'cli';

    /**
     * @var the mode of the service
     */
    protected $_mode;

    /**
     * @var Zend_Console_GetOpt arguments
     */
    protected $_consoleOpt;

    /**
     * Set the Zend_Console_GetOpt object
     * the arguments passed from the CLI
     */
    public function setConsoleOpt($opt) 
    {
        $this->_consoleOpt = $opt;
    }

    /**
     * Print the message to the user
     *
     * @param string $message message to print
     */
    public function message($message)
    {
        if ($this->_mode == self::SERVICE_MODE_WEB) {
            return;
        }
        echo PHP_EOL . $message . PHP_EOL;
    }

    public function setMode($mode)
    {
        $this->_mode = $mode;
        return $this;
    }
}

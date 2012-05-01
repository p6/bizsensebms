<?php
/**
 * View helper to print accounts in a dashlet
 *
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
 */

/** 
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class BV_View_Helper_AccountDashlet extends Zend_View_Helper_Abstract
{

    /**
     * @return the HTML table to be printed as is
     */
    public function accountDashlet($select)
    {
	    $output = '';
        $account = new Core_Model_Account;
        $accountData = $account->getAccounts();        
        $output .= "<table class=\"data_table\">";
        $output .= "<tr><th class=\"short\">My Accounts</th></tr>";
        foreach ($accountData as $row) {
            $output .= "<tr><td>";
            $accountId = $row->account_id;
            $url = $this->view->url(
                array(
                    'module' => 'default',
                    'controller' => 'account',
                    'action' =>  'viewdetails',
                    'account_id' => htmlspecialchars($accountId),
                ), 'default', true
            );
            $output .=  "<a href=\"" . $url . "\">";    
            $output .= htmlspecialchars($row->account_name);
            $output .= "</a>";
            $output .= "</td></tr>";
        }
    	$output .= "</table>";
        return $output;
    }
}



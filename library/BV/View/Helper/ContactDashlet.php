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
class BV_View_Helper_ContactDashlet extends Zend_View_Helper_Abstract
{

    public function contactDashlet($select)
    {
	    $output = '';
        $contact = new Core_Model_Contact;
        $contactData = $contact->getContacts(); 
        $contactRecord = $contactData->toArray();
        if (!$contactRecord) {
            $output .= "<ul>";
            $output .= "<li>No contacts to display</li>";
            $output .= "</ul>";
            return $output;
        }        
        $output .= "<ul>";
        foreach ($contactData as $row) {
            $output .= "<li>";
            $contactId = $row->contact_id;
            $url = $this->view->url(
                array(
                    'module' => 'default',
                    'controller' => 'contact',
                    'action' => 'viewdetails',
                    'contact_id' => htmlspecialchars($contactId),
                ), 'default',true
            );
            $output .=  "<a href=\"" . $url . "\">";    
            $fullName = $row->first_name . " " . $row->middle_name . " " . $row->last_name;
            if (strlen($fullName) > 30) {
                $name = substr($fullName, 0, 30);
                $name .= " ...";
                $output .= htmlspecialchars($name);
            }  
            else {
                $output .= htmlspecialchars($fullName);
            }
            $output .= "</a>";
            $output .= "</li>";
        }
    	$output .= "</ul>";
        return $output;
    }
}



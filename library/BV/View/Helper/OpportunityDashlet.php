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

class BV_View_Helper_OpportunityDashlet extends Zend_View_Helper_Abstract
{
    public function opportunityDashlet($select)
    {
	    $output = '';
        $opportunity = new Core_Model_Opportunity;
        $opportunityData = $opportunity->getOpportunities();
        $opportunityRecord = $opportunityData->toArray();
       
        if (!($opportunityRecord)) {
            $output .= "<ul>";
            $output .= "<li>No opportunities to display</li>";
            $output .= "</ul>";
            return $output;
        }
        $output .= "<ul>";
        foreach ($opportunityData as $row) {
            $output .= "<li>";
            $opportunityId = $row->opportunity_id;
            $url = $this->view->url(
                array(
                    'module' => 'default',
                    'controller' => 'opportunity',
                    'action' => 'viewdetails',
                    'opportunity_id' =>  htmlspecialchars($opportunityId)
                ), 'default', true
            );
            $output .=  "<a href=\" " .$url . "\">";    
            if (strlen($row->name) > 30) {
                $name = substr($row->name, 0, 30);
                $name .= " ...";
                $output .= htmlspecialchars($name);
            }  
            else {
                $output .= htmlspecialchars($row->name);
            }
            $output .= "</a>";
            $output .= "</li>";
        }
    	$output .= "</ul>";
        return $output;
    }
}



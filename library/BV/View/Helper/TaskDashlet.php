<?php
/**
 * View helper to print tasks in a dashlet
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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class BV_View_Helper_TaskDashlet extends Zend_View_Helper_Abstract
{

    /**
     * @return the HTML table to be printed as is
     */
    public function taskDashlet($select)
    {
	    $output = '';
        $task = new Core_Model_Activity_Task;
        $taskData = $task->getTasks(); 
        $taskRecord = $taskData->toArray();
        if (!$taskRecord) {
            $output .="<ul>";
            $output .="<li>No tasks to display</li>";
            $output .="</ul>";
            return $output;
        }         
        $output .= "<ul>";
        foreach ($taskData as $row) {
            $output .= "<li>";
            $taskId = $row->task_id;
            $url = $this->view->url(
                array(
                    'module' => 'activity',
                    'controller' => 'task',
                    'action' =>  'viewdetails',
                    'task_id' => htmlspecialchars($taskId),
                ), 'default', true
            );
            $output .=  "<a href=\"" . $url . "\">";    
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

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

class BV_View_Helper_CallDashlet extends Zend_View_Helper_Abstract
{

    /**
     * @return the HTML table to be printed as is
     */
    public function callDashlet($select)
    {
	    $output = '';
        $call = new Core_Model_Activity_Call;
        $callData = $call->getCalls();  
        $callRecord = $callData->toArray();
        if (!$callRecord) {
            $output .= "<ul>";
            $output .= "<li>No calls to display</li>";
            $output .= "</ul>";
            return $output;
        }        
        $output .= "<ul>";
        foreach ($callData as $row) {
            $output .= "<li>";
            $callId = $row->call_id;
            $url = $this->view->url(
                array(
                    'module' => 'activity',
                    'controller' => 'call',
                    'action' =>  'viewdetails',
                    'call_id' => htmlspecialchars($callId),
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



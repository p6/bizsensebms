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



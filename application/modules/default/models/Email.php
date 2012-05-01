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

class Core_Model_Email extends BV_Model_Essential_Abstract
{
    
    /**
     * Store the email configuration in the db
     */
    public function configure($data)
    {
        $variableModel = new Core_Model_Variable;
        $variableModel->save('email',$data['email']);
        $variableModel->save('from_name',$data['from_name']);
        $variableModel->save('transport',$data['transport']);
        $variableModel->save('smtp_server',$data['smtp_server']);
        $variableModel->save('smtp_require_auth',$data['smtp_require_auth']);
        $variableModel->save('smtp_auth',$data['smtp_auth']);
        $variableModel->save('smtp_username',$data['smtp_username']);
        $variableModel->save('smtp_password',$data['smtp_password']);
        $variableModel->save('smtp_secure_connection',$data['smtp_secure_connection']);
        $variableModel->save('smtp_port',$data['smtp_port']);
        $variableModel->save('footer',$data['footer']);
                                        
        /*$exists = $this->db->fetchOne("SELECT * FROM site_email");

        if ($exists) {
            $this->db->update('site_email', $data);
        } else {
            $this->db->insert('site_email', $data);
        }*/
    }   
    
    /**
     * @param array 
     * test email configuration
     */
    public function testMail($data)
    {  
        $mail = new Core_Service_Mail;
        $mail->setBodyText($data['message']);
        $mail->addTo($data['email'], 'test');
        $mail->setSubject($data['subject']);
        try  {  
            $mail->send();
        }   
        catch (Zend_Exception $e) {
            $log = new Core_Service_Log;
            $info = 'Failed in connecting mail server';
            $log->info($info);
            return false;
        }      
        return true;
    } 
}

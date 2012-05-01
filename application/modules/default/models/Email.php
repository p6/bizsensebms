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

/*
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
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

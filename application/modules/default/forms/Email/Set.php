<?php
/*
 * BizSense administration email set form
 *
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */


class Core_Form_Email_Set extends Zend_Form
{

    public function init()
    {
        $db = Zend_Registry::get('db');
        $this->setAction('/admin/email/set')
                ->setMethod('post');
        
        $email = $this->createElement('text', 'email')
                        ->setLabel('Site email')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_EmailAddress());

        $fromName = $this->createElement('text', 'from_name')
                        ->setLabel('From Name')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(0, 100));


        $transport = $this->createElement('select', 'transport')
                        ->setLabel('Transport');
        $transport->addMultiOptions(array('SMTP'=>'SMTP'));    
        $transport->addMultiOptions(array('sendmail'=>'sendmail')); 
    
        $smtpServer = $this->createElement('text', 'smtp_server')    
                            ->setLabel('SMTP Server Name');

        $smtpRequireAuth = $this->createElement('checkbox', 'smtp_require_auth')
                                    ->setLabel('Server reuires authentication');
            
        $smtpAuth = $this->createElement('select', 'smtp_auth')
                        ->setLabel('SMTP Authentication type');
        $smtpAuth->addMultiOptions(array('plain'=>'PLAIN')); 
        $smtpAuth->addMultiOptions(array('login'=>'LOGIN')); 
        $smtpAuth->addMultiOptions(array('crammd5'=>'CRAM-MD5')); 


        $smtpUsername = $this->createElement('text', 'smtp_username')    
                            ->setLabel('SMTP Username');
        $smtpPassword = $this->createElement('password', 'smtp_password')    
                            ->setLabel('SMTP Password');

        $smtpSecureConnection = $this->createElement('select', 'smtp_secure_connection')
                                    ->setLabel('SMTP Secure Connection');

        $smtpSecureConnection->addMultiOptions(array('no'=>'No')); 
        $smtpSecureConnection->addMultiOptions(array('tls'=>'TLS')); 
        $smtpSecureConnection->addMultiOptions(array('ssl'=>'SSL')); 
   
        $smtpPort = $this->createElement('text', 'smtp_port')
                            ->setValue('25')
                            ->setAttrib('size', '2')
                            ->setLabel('SMTP Port'); 
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');

        $footer = $this->createElement('textarea', 'footer')
                        ->setLabel('Email Footer')  
                        ->setDescription('This text will be appended to all outgoing emails')
                        ->setAttribs(array('cols'=>'40', 'rows'=>'5'));   
 
        $this->setElementFilters(array('StringTrim'));
        $this->addElements(array($email, $fromName, $transport, $smtpServer, 
            $smtpAuth, $smtpRequireAuth, $smtpUsername, 
            $smtpPassword, $smtpSecureConnection, $smtpPort, $footer, $submit));

        /*
         * Populate the form
         */
        $result = array();
        $variableModel = new Core_Model_Variable;
        
        $variableModel->setVariable('email');
        $result['email'] = $variableModel->getValue();
        
        $variableModel->setVariable('from_name');
        $result['from_name'] = $variableModel->getValue();
        
        $variableModel->setVariable('transport');
        $result['transport'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_server');
        $result['smtp_server'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_require_auth');
        $result['smtp_require_auth'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_username');
        $result['smtp_username'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_secure_connection');
        $result['smtp_secure_connection'] = $variableModel->getValue();
        
        $variableModel->setVariable('smtp_port');
        $result['smtp_port'] = $variableModel->getValue();
        
        $variableModel->setVariable('footer');
        $result['footer'] = $variableModel->getValue();
        
        
        if ($result) {
            $this->populate($result); 
        }
    }
}



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
 * @package    BV_Lib_Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt.
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Service_Mail extends Zend_Mail
{
    /**
     *@TODO deprecated
     */
    public $db;

    /**
     * @var object Zend_Mail_Transport
     */
    protected $transport;

    /**
     * @var string the body text
     */
    protected $bodyText;

    /**
     * @var string 
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;
        
    /*
     * Zend_Mail object
     */
    protected $mail;
       
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = Zend_Registry::get('db');
        //$this->mail = new Zend_Mail();
        $this->setTransport(); 
        $this->setFrom();
    }

    /**
     * Set the body text append add the footer
     * @param string $bodyText
     * return fluent interface
     */
    public function setBodyText($bodyText = "test body text",$charset = null,
                 $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }
        $varibaleModel = new Core_Model_Variable('footer');
        $footer = $varibaleModel->getValue();
        if (!$footer) {
            $footer = 'Email was sent from Binary Vibes BizSense';
        }
        $bodyText .= "\n" . "\n";
        $bodyText .= $footer;
        
        $mp = new Zend_Mime_Part($bodyText);
        $mp->encoding = $encoding;
        $mp->type = Zend_Mime::TYPE_TEXT;
        $mp->disposition = Zend_Mime::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->_bodyText = $mp;

        $this->_bodyText = $mp;
        return $this;
    }

    /**
     * Set the from name and email address
     * @param string $fromEmail
     * @param string $fromName
     * @return fluent interface
     */
    public function setFrom(
        $fromEmail = 'user@example.com', $fromName = 'BizSense CRM And ERP')
    {
        $varibaleModel = new Core_Model_Variable('email');
        $fromEmail = $varibaleModel->getValue();
        if (!$fromEmail) {
            $fromEmail = 'bizsense@' . $_SERVER['SERVER_NAME'];
        }
        $varibaleModel = new Core_Model_Variable('from_name');
        $fromName = $varibaleModel->getValue();
        if (strlen($fromName) < 2) {
            $fromName = 'BizSense Site Mailer';
        }
        
        if (null !== $this->_from) {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('From Header set twice');
        }

        $email = $this->_filterEmail($fromEmail);
        $name  = $this->_filterName($fromName);
        $this->_from = $fromEmail;
        $this->_storeHeader('From', $this->_formatAddress($fromEmail, $fromName), true);
        
        return $this;
    }

 
    /**
     * Set email server transport - SMTP or sendmail
     * @TODO fetch the record from Mail model
     */
    public function setTransport()
    {
        $varibaleModel = new Core_Model_Variable;
        $varibaleModel->setVariable('transport');
        $transport = $varibaleModel->getValue();
        if ($transport == "SMTP") {
            $varibaleModel->setVariable('smtp_server'); 
            $smtpServer = $varibaleModel->getValue();
            $varibaleModel->setVariable('smtp_require_auth'); 
            $requireAuth = $varibaleModel->getValue();
            $varibaleModel->setVariable('smtp_secure_connection'); 
            $secureConnection = $varibaleModel->getValue();

            if ($requireAuth) {
                $varibaleModel->setVariable('smtp_auth'); 
                $auth = $varibaleModel->getValue();
                
                $varibaleModel->setVariable('smtp_username');
                $smtpUsername = $varibaleModel->getValue();
                
                $varibaleModel->setVariable('smtp_password');
                $smtpPassword = $varibaleModel->getValue();
                
                $varibaleModel->setVariable('smtp_port');
                $smtpPort = $varibaleModel->getValue();
                
                $config = array(
                    'auth' => $auth,
                    'username' => $smtpUsername,
                    'password' => $smtpPassword,
                    'port'     => $smtpPort,
                );
                if($secureConnection != 'no') {
                    $config['ssl'] = $secureConnection;
                }
                $tr = new Zend_Mail_Transport_Smtp($smtpServer, $config);
            } else {
                $tr = new Zend_Mail_Transport_Smtp($smtpServer);
            
            }  
            $this->transport = $tr; 

            $this->setDefaultTransport($tr);
        }
    }    

    /**
     * Send the email
     * @return bool
     */
    public function send($transport = null)
    {
        if ($transport === null) {
            if (! self::$_defaultTransport instanceof Zend_Mail_Transport_Abstract) {
                require_once 'Zend/Mail/Transport/Sendmail.php';
                $transport = new Zend_Mail_Transport_Sendmail();
            } else {
                $transport = self::$_defaultTransport;
            }
        }

        if ($this->_date === null) {
            $this->setDate();
        }

        if(null === $this->_from && null !== self::getDefaultFrom()) {
            $this->setFromToDefaultFrom();
        }

        if(null === $this->_replyTo && null !== self::getDefaultReplyTo()) {
            $this->setReplyToFromDefault();
        }

        $transport->send($this);

        return $this;
    }
}

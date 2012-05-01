<?php
/*
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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Service_Install_Form_Create extends Zend_Form
{
    public function init()
    {
        $this->setAction('/install/index');
        $this->setMethod('post');

        $dbDriver = $this->createElement('select', 'db_driver')
            ->setLabel('Database Driver')
            ->addMultiOptions(array("Pdo_Mysql"=>"Pdo_Mysql"))
            ->setRequired(true);

        $dbHostname = $this->createElement('text', 'db_hostname')
            ->setRequired(true)
            ->setLabel('Database Hostname')
            ->setValue('localhost')
            ->addValidator(
                new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_ALL));

        $dbHostname->setDescription("The hostname of the database server");
    
        $dbName = $this->createElement('text', 'db_name');
        $dbName->setLabel('Database name');
        $dbName->setRequired(true);

        $dbName->setDescription("The above database must exist on the server.");
    
        $dbUsername = $this->createElement('text', 'db_username');
        $dbUsername->setLabel('Database username');
        $dbUsername->setRequired(true);

        $dbUsername->setDescription("Username that can access the database");
        $dbPassword = $this->createElement('password', 'db_password')
                ->setLabel('Database password')
            ->setRequired(true)
            ->addValidator(new BV_Validate_DbConnection());

        $adminFirstName = $this->createElement('text', 'first_name')
                                ->addValidator(new Zend_Validate_StringLength(0, 100))
                                ->setLabel('Admin First Name');

        $adminMiddleName = $this->createElement('text', 'middle_name')
                                ->addValidator(new Zend_Validate_StringLength(0, 100))
                                ->setLabel('Admin Middle Name');

        $adminLastName = $this->createElement('text', 'last_name')
                                ->setLabel('Admin Last Name')
                                ->addValidator(new BV_Validate_PersonName);


        $adminEmail = $this->createElement('text', 'admin_email');
        $adminEmail->setLabel('Admin e-mail');
        $adminEmail->setRequired(true)
            ->addValidator(new Zend_Validate_EmailAddress());
        $adminEmail->setDescription("Your email address is your username. This user will have unrestricted
             access throught BizSense.");

        $username = $this->createElement('text', 'username');
        $username->setLabel('Username');
        $username->setRequired(true);



        $password = $this->createElement('password', 'password')
                ->setRequired(true)
                ->addValidator('StringLength', false, array(6, 20))
                ->setLabel('Admininstator password');
        $passwordConfirm = $this->createElement('password', 'password_confirm')
                ->setRequired(true)
                ->setLabel('Confirm Admininstator password')
                ->addValidator(new BV_Validate_MatchPasswords());

        $companyName = $this->createElement('text', 'company_name')
                                ->setLabel('Organization Name')
                                ->addValidator(new Zend_Validate_StringLength(0, 150))
                                ->setRequired(true);

        $branchName = $this->createElement('text', 'branch_name')
                            ->setLabel('Default Branch Name')
                            ->addValidator(new Zend_Validate_StringLength(0, 200))
                            ->setRequired(true);

        $addressLine1 = $this->createElement('text', 'address_line_1')
                            ->setLabel('Address Line 1')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setRequired(true);

        $addressLine2 = $this->createElement('text', 'address_line_2')
                            ->setLabel('Address Line 2')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setRequired(true);

        $addressLine3 = $this->createElement('text', 'address_line_3')
                            ->setLabel('Address Line 3')
                            ->addValidator(new Zend_Validate_StringLength(0, 100));
        
        $addressLine4 = $this->createElement('text', 'address_line_4')
                            ->setLabel('Address Line 4')
                            ->addValidator(new Zend_Validate_StringLength(0, 100));
 
        $city = $this->createElement('text', 'city')
                        ->setLabel('City')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(0, 100));

        $state = $this->createElement('text', 'state')
                        ->setLabel('State')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(0, 100));

        $postalCode = $this->createElement('text', 'postal_code')
                        ->setLabel('Postal Code')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(0, 10));

        $country = $this->createElement('text', 'country')
                        ->setLabel('Country')
                        ->setRequired(true)
                        ->addValidator(new Zend_Validate_StringLength(0, 100));
                       

        $submit = $this->createElement('submit', 'submit')
                      ->setAttrib('class','submit_button');
        $this->addElements(array($dbDriver, $dbHostname, $dbName, $dbUsername, $dbPassword, $adminFirstName,
            $adminMiddleName, $adminLastName, $adminEmail, $username, $password, $passwordConfirm, $companyName, $branchName, 
            $addressLine1, $addressLine2, $addressLine3,  $addressLine3, $addressLine4, $city, $state, $postalCode, 
            $country, $submit));
        
    }

}


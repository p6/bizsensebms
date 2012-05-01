<?php
/**
 * @file this scipt is designed to run on the command line
 * This is a distribution file only
 * Cppy and paste this file to myInstallerValues.php 
 * And edit the file as per your needs
 */

/**
 * The variable containing the values to be posted to the installation URL
 */
$myVariables = array(
    'postValues' => array(
        'db_driver'          =>  'Pdo_Mysql',
        'db_hostname'        =>  'localhost',
        'db_name'            =>  'biz_1',
        'db_username'        =>  'biz_1',
        'db_password'        =>  'password',
        'password'          =>  'password',
        'password_confirm'   =>  'password',
        'admin_email'        =>  'sudheer.s@sudheer.net',
        'username'        =>  'admin',
        'first_name'        =>  'Sudheer',
        'middle_name'       =>  '',
        'last_name'         =>  'S',
        'company_name'       =>  'Binary Vibes',
        'branch_name'        =>  'Head Quarters',
        'address_line_1'      =>  '#506, 10 B Main Road',
        'address_line_2'      =>  'I Block',
        'address_line_3'      =>  'Jayanagar',
        'address_line_4'      =>  '',
        'city'              =>  'Bangalore',
        'state'             =>  'Karnataka',
        'postal_code'        =>  '560011',
        'country'           =>  'India',
        'Submit'            =>  'Submit',
    ),
    'url' => 'http://biz.binaryvibes.co.in/install/index',
    'privileged_db_username'  =>  'root',
    'privileged_db_user_password' => 'password',
);

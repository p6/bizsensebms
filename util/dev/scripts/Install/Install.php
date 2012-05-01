#!/usr/bin/php
<?php
chdir(realpath(dirname(__FILE__)));

define('APPLICATION_PATH', realpath(dirname(__FILE__)) . '/../../../../');

//Add library directory to the include_path
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/library',
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);


/**
 * Remove the existing config file
 */
@$deleted = unlink(APPLICATION_PATH . '/application/configs/database.ini');
include APPLICATION_PATH . '/util/dev/scripts/Install/myInstallerValues.php';

/**
 * Drop the existing database and then create it again
 */
$mysqli = new mysqli($myVariables['postValues']['db_hostname'], $myVariables['privileged_db_username'], $myVariables['privileged_db_user_password'], $myVariables['postValues']['db_name']);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
$mysqli->query("DROP DATABASE " . $myVariables['postValues']['db_name']);
$mysqli->query("CREATE DATABASE " . $myVariables['postValues']['db_name']);


$client = new Zend_Http_Client($myVariables['url']);
$client->setParameterPost($myVariables['postValues']);
$response = $client->request('POST');

echo $response->getBody();



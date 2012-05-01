<?php 
error_reporting( E_ALL | E_STRICT );

define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));
define('APPLICATION_PATH', BASE_PATH . '/application');


// Include path
set_include_path(
    '.'
    . PATH_SEPARATOR . BASE_PATH . '/library'
    . PATH_SEPARATOR . get_include_path()
);

// Define application environment
define('APPLICATION_ENV', 'testing');

/** Zend_Application */
require_once 'Zend/Application.php';  

require_once 'Zend/Session.php';
Zend_Session::$_unitTestEnabled = true;

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();


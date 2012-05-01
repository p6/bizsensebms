#!/usr/bin/php
<?php

/**
 * Usage
 1. To turn the environent into production
php dev/scripts/Build/Productionize.php
 2. To turn the environment into development 
php dev/scripts/Build/Productionize.php reverse
 */

$from = '';
if (isset($argv[1])) {
    $from = $argv[1];
}

$currentFilePath = realpath(dirname(__FILE__));
// // // // // // // // // require_once $currentFilePath . '/../CliInit.php';

$pathToPublic_Html = $scriptsPath . '/../../../public_html';

$htAccessFile = $pathToPublic_Html . '/.htaccess';

/**
 * Change the environment variable in .htaccess
 */
$command = "sed -i 's/SetEnv APPLICATION_ENV development/SetEnv APPLICATION_ENV production/g' $htAccessFile <$htAccessFile ";

if ($from == 'reverse') {
    $command = "sed -i 's/SetEnv APPLICATION_ENV production/SetEnv APPLICATION_ENV development/g' $htAccessFile <$htAccessFile ";
}


$stingToWrite = exec($command);

echo "\n" . "Done" . "\n";


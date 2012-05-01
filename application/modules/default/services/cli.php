<?php
require_once dirname(realpath(__FILE__)) . '/CliBootstrap.php';
$cli = new Core_Service_CliBootstrap;
$cli->init();
$cli->run();

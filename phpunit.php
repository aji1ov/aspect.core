<?php

use Aspect\Lib\Application;
use Aspect\Lib\Gateway;

const NO_KEEP_STATISTIC = true;
const NOT_CHECK_PERMISSIONS = true;
const LOG_FILENAME = 'php://stderr';
const ASPECT_INIT_CONTEXT = 'test';

include __DIR__."/../../php_interface/vendor/autoload.php";
$_SERVER['DOCUMENT_ROOT']  = realpath(__DIR__.'/../../..');
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$app = Application::getInstance();

if($app->gateway() !== Gateway::CLI) {
    die('Bad gateway');
}

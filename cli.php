<?php

use Aspect\Lib\Application;
use Aspect\Lib\Context;
use Aspect\Lib\Exception\CommandException;
use Aspect\Lib\Facade\Command;
use Aspect\Lib\Facade\Queue;
use Aspect\Lib\Gateway;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Aspect\Lib\Service\Pretty\Exception as PrettyException;

$_SERVER['DOCUMENT_ROOT']  = dirname(__DIR__, 3);
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$app = Application::getInstance();

if ($app->gateway() !== Gateway::CLI) {
    die('Bad gateway');
}

$app->bound(Context::CLI, function(InputInterface $input, OutputInterface $output, PrettyException $pretty) use ($app) {
    global $argv;
    try {
        Command::call($argv[1], $input, $output);
    } catch (\Throwable $e) {
        notice($pretty->makePretty($e));
    }
});




<?php

namespace Aspect\Lib\Service\Logger;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Context;
use Aspect\Lib\Struct\ServiceLocator;
use Bitrix\Main\Diag\FileLogger;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;

class Monolog extends Logger
{
    use ServiceLocator;

    #[Fetch]
    protected Context $context;

    public function __construct() {
        parent::__construct('system');

        $this->pushHandler(
            new PsrHandler(
                new FileLogger($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/logs/'.$this->context->value.'.log.txt')
            )
        );

        $this->pushHandler(IgnitionLogger::getInstance());
    }
}
<?php

namespace Aspect\Lib\Service\Noticer;

use Aspect\Lib\Support\Interfaces\NoticerInterface;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\JsonSerializableDateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;

class BrowserNoticer implements NoticerInterface
{

    public function notice(...$arguments): void
    {
        (new BrowserConsoleHandler())->handle(
            new LogRecord(
                new JsonSerializableDateTimeImmutable(false),
                'notice',
                Level::Notice,
                implode("," , $arguments)
            )
        );
    }
}
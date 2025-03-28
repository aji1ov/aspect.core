<?php

namespace Aspect\Lib\Service\Noticer;

use Aspect\Lib\Blueprint\DI\Fetch;
use Psr\Log\LoggerInterface;

class InLogNoticer extends FormattedNoticer
{

    #[Fetch]
    private LoggerInterface $logger;

    public function notice(...$arguments): void
    {
        $this->logger->notice($this->formatter->format(...$arguments)."\n");
    }
}
<?php

namespace Aspect\Lib\Service\Noticer;

use Aspect\Lib\Support\Interfaces\NoticerInterface;

class MutedNotifier implements NoticerInterface
{

    public function notice(...$arguments): void
    {
    }
}
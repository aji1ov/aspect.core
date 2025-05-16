<?php

namespace Aspect\Lib\Preset\Runtime;

use Aspect\Lib\Application;
use Aspect\Lib\Support\Interfaces\RuntimeInterface;

class Carbon implements RuntimeInterface
{

    public function onBitrixLoaded(): void
    {
        \Carbon\Carbon::setLocale('ru');
    }

    public function onReady(Application $application): void
    {
    }
}
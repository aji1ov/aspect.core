<?php

namespace Aspect\Lib\Preset\Runtime;

use Aspect\Lib\Application;
use Aspect\Lib\Event\Repository;
use Aspect\Lib\Support\Interfaces\RuntimeInterface;

class Events implements RuntimeInterface
{

    public function onBitrixLoaded(): void
    {
        // TODO: Implement onBitrixLoaded() method.
    }

    public function onReady(Application $application): void
    {
        (new Repository())->makeEvents();
    }
}
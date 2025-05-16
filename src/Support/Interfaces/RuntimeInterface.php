<?php

namespace Aspect\Lib\Support\Interfaces;

use Aspect\Lib\Application;

interface RuntimeInterface
{
    public function onBitrixLoaded(): void;
    public function onReady(Application $application): void;
}
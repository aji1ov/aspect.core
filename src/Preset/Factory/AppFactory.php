<?php

namespace Aspect\Lib\Preset\Factory;

use Aspect\Lib\Application;
use Aspect\Lib\Context;
use Aspect\Lib\Service\DI\Factory;
use function Aspect\Lib\DI\singleton;

class AppFactory implements Factory
{
    public function bind(): array
    {
        return [
            Application::class => singleton(fn () => Application::getInstance()),
            Context::class => fn (Application $app) => $app->context(),
        ];
    }
}
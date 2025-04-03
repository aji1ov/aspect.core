<?php

namespace Aspect\Lib\Struct;

use Aspect\Lib\Application;

trait ServiceLocator
{
    /**
     * @throws \Exception
     */
    public static function getInstance(): static
    {
        return Application::getInstance()->get(static::class);
    }

    /**
     * @throws \Exception
     */
    public static function fake(string $fakeClass): void
    {
        Application::getInstance()->fake()->add(static::class, $fakeClass);
    }
}
<?php

namespace Aspect\Lib\DI\Factory;

use Aspect\Lib\Application;
use Aspect\Lib\DI\Container;
use Aspect\Lib\DI\Factory;
use ReflectionException;

class FakeFactory extends Factory
{
    private SingletonFactory $fake;
    public function __construct(string $fakeClass)
    {
        $this->fake = new SingletonFactory(fn (Application $application) => $application->get($fakeClass));
    }

    /**
     * @throws ReflectionException
     */
    public function make(Container $container)
    {
        return $this->fake->make($container);
    }
}
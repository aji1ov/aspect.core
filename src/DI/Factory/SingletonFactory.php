<?php

namespace Aspect\Lib\DI\Factory;

use Aspect\Lib\DI\Container;

class SingletonFactory extends MakeFactory
{
    private static array $instances = [];
    private static int $index = 0;

    private int $current;

    public function __construct(callable $closure)
    {
        parent::__construct($closure);
        $this->current = static::$index++;
    }

    public function make(Container $container)
    {
        if (!static::$instances[$this->current]) {
            static::$instances[$this->current] = parent::make($container);
        }

        return static::$instances[$this->current];
    }

    private function getInstances()
    {
        $instances = [];
        foreach (static::$instances as $index => $instance) {
            $instances[$index] = get_class($instance);
        }

        return $instances;
    }
}
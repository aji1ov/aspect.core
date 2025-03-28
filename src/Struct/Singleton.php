<?php

namespace Aspect\Lib\Struct;

use Aspect\Lib\Blueprint\Ignore;
use Exception;

trait Singleton
{
    private static array $instance = [];
    private static array $make = [];

    /**
     * @throws \RuntimeException
     */
    #[Ignore]
    public static final function getInstance(): static
    {
        $called_class = get_called_class();
        if(!static::$instance[$called_class]) {

            if(static::$make[$called_class]) {
                throw new \RuntimeException("Circular singleton call");
            }

            static::$make[$called_class] = true;

            static::$instance[$called_class] = new $called_class();
            $afterMake = static::$instance[$called_class]->make();
            unset(static::$make[$called_class]);

            if($afterMake) {
                $afterMake();
            }

        }

        return static::$instance[$called_class];
    }

    private function __clone(){}
    private function __wakeup(){}
    private function __construct() {
        if(static::$instance[static::class]) {
            throw new Exception('Multiple singleton initialization');
        }
    }

    protected function make(): ?callable
    {
        return null;
    }
}
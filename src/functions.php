<?php

namespace Aspect\Lib\DI {

    use Aspect\Lib\DI\Factory\MakeFactory;
    use Aspect\Lib\DI\Factory\SingletonFactory;

    function singleton(callable $closure): Factory
    {
        return new SingletonFactory($closure);
    }

    function object(mixed $object): Factory
    {
        return singleton(fn() => $object);
    }

    function factory(callable $closure): Factory
    {
        return new MakeFactory($closure);
    }
}
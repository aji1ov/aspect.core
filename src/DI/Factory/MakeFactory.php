<?php

namespace Aspect\Lib\DI\Factory;

use Aspect\Lib\DI\Container;
use Aspect\Lib\DI\Factory;
use Aspect\Lib\DI\Invoke\FunctionInvoker;

class MakeFactory extends Factory
{
    private \Closure $closure;
    public function __construct(callable $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @throws \ReflectionException
     */
    public function make(Container $container)
    {
        return FunctionInvoker::invoke(new \ReflectionFunction($this->closure), $container, null);
    }

    protected function getClosure(): \Closure
    {
        return $this->closure;
    }
}
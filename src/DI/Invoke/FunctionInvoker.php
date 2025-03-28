<?php

namespace Aspect\Lib\DI\Invoke;

use Aspect\Lib\DI\Container;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;

class FunctionInvoker
{
    /**
     * @throws ReflectionException
     */
    public static function invoke(ReflectionFunction|ReflectionMethod $closure, Container $container, $object = null, ?array $parameters = []): mixed
    {
        $injectedParameters = [];

        foreach ($closure->getParameters() as $parameter) {
            if ($type = $parameter->getType()) {
                if ($type instanceof ReflectionNamedType) {
                    if(isset($parameters[$parameter->getName()])) {
                        $injectedParameters[] = $parameters[$parameter->getName()];
                    } else if (!$type->isBuiltin()) {
                        $injectedParameters[] = $container->get($type->getName());
                    }
                }
            }

        }

        if ($closure instanceof ReflectionFunction) {
            return $closure->invoke(...$injectedParameters);
        }
        return $closure->invoke($object, ...$injectedParameters);
    }
}
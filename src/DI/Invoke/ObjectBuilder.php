<?php

namespace Aspect\Lib\DI\Invoke;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\DI\Container;
use Aspect\Lib\DI\FakeInterface;

/**
 * @template T
 */
class ObjectBuilder
{
    private string $objectClass;
    private Container $container;

    /**
     * @param Container $container
     * @param class-string<T> $objectClass
     */
    public function __construct(Container $container, string $objectClass)
    {
        $this->container = $container;
        $this->objectClass = $objectClass;
    }

    /**
     * @return T
     * @throws \ReflectionException
     */
    public function newInstance()
    {
        $rc = new \ReflectionClass($this->objectClass);
        $object = $rc->newInstanceWithoutConstructor();

        $this->fetchObjectPropertiesDependencies($object);
        $this->callConstructor($object);

        return $object;
    }

    /**
     * @param T $object
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function fetchObjectPropertiesDependencies($object): void
    {
        static::injectPropertyDependencies($this->container, $object);
    }

    /**
     * @throws \Exception
     */
    public static function injectPropertyDependencies(Container $container, $object): void
    {
        $rc = new \ReflectionClass(get_class($object));
        foreach ($rc->getProperties() as $property) {
            static::fetchPropertyDependency($container, $property, $object);
        }

        while ($rc = $rc->getParentClass()) {
            foreach ($rc->getProperties() as $property) {
                static::fetchPropertyDependency($container, $property, $object);
            }
        }
    }

    /**
     * @param Container $container
     * @param \ReflectionProperty $property
     * @param T $object
     * @return void
     * @throws \Exception
     */
    private static function fetchPropertyDependency(Container $container, \ReflectionProperty $property, $object): void
    {
        if($attributes = $property->getAttributes(Fetch::class)) {

            $realOnly = $object instanceof FakeInterface;

            $fetchAttribute = $attributes[0]->newInstance();
            assert(is_a($fetchAttribute, Fetch::class, true));

            $service = null;

            if(!$fetchAttribute->getName() && $type = $property->getType()) {
                $service = $type->getName();
            } else if ($fetchAttribute->getName()) {
                $service = $fetchAttribute->getName();
            }

            if(!$service) {
                throw new \Exception("#[Fetch] unknown dependency name");
            }

            $property->setValue($object, $container->getService($service, $realOnly));
        }
    }

    /**
     * @param T $object
     * @return void
     * @throws \ReflectionException
     */
    protected function callConstructor($object): void
    {
        $rc = new \ReflectionClass($this->objectClass);
        if($constructor = $rc->getConstructor()) {
            FunctionInvoker::invoke($constructor, $this->container, $object);
        }
    }
}
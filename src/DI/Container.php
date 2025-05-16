<?php

namespace Aspect\Lib\DI;

use Aspect\Lib\DI\Invoke\ObjectBuilder;
use Exception;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{

    private Locator $locator;
    private array $processed = [];

    public function __construct(Locator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @template T
     * @param string|class-string<T> $id
     * @return T
     * @throws Exception
     */
    public function get(string $id)
    {
        return $this->getService($id);
    }

    /**
     * @template T
     * @param string|class-string<T> $id
     * @return T
     * @throws Exception
     */
    public function getService(string $id, bool $realOnly = false)
    {
        if(!$realOnly && isset($this->processed[$id])) {
            throw new \RuntimeException("Circular dependency injection detected");
        }
        $service = null;

        if($this->locator->hasFactory($id)) {
            $this->busy($id);
            $service = $this->locator->getFactory($id, $realOnly)->make($this);
        }

        if(!$service && class_exists($id)) {
            $rc = new \ReflectionClass($id);
            if(!$rc->isAbstract() && !$rc->isInterface() && !$rc->isTrait()) {
                $this->busy($id);
                $builder = new ObjectBuilder($this, $id);
                $service = $builder->newInstance();
            }
        }

        if($service) {
            $this->release($id);
            return $service;
        }

        throw new Exception('no Factory for service:' . $id);
    }

    public function has(string $id): bool
    {
        return $this->locator->hasFactory($id);
    }

    private function busy(string $id) {
        $this->processed[$id] = true;
    }

    private function release(string $id) {
        unset($this->processed[$id]);
    }
}
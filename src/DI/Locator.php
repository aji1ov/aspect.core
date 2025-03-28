<?php

namespace Aspect\Lib\DI;

use Aspect\Lib\DI\Factory\FakeFactory;
use Exception;

class Locator
{
    /** @var Factory[] */
    private array $serviceFactories = [];

    private array $fakes = [];

    public function hasFactory(string $service): bool
    {
        return isset($this->serviceFactories[$service]);
    }

    protected function hasFake(string $service): bool
    {
        return isset($this->fakes[$service]);
    }

    /**
     * @throws Exception
     */
    public function getFactory(string $service, bool $realOnly = false): Factory
    {
        if(!$realOnly && $this->hasFake($service)) {
            return $this->fakes[$service];
        }

        if(!$this->hasFactory($service)) {
            throw new Exception("no factory for service: ". $service);
        }

        return $this->serviceFactories[$service];
    }

    public function addFactory(string $service, Factory $factory): void
    {
        $this->serviceFactories[$service] = $factory;
    }

    public function addFake(string $service, string $fakeClass): void
    {
        $this->fakes[$service] = new FakeFactory($fakeClass);
    }
}
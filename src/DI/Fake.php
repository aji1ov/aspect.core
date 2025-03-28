<?php

namespace Aspect\Lib\DI;

use Aspect\Lib\Application;

class Fake
{
    private Locator $locator;
    public function __construct(Locator $locator)
    {
        $this->locator = $locator;
    }

    public function add(string $service, string $fakeClass): void
    {
        $this->locator->addFake($service, $fakeClass);
    }
}
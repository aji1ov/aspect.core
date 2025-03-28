<?php

namespace Aspect\Lib\PhpUnit\Faker;

use Closure;

abstract class FakerRule
{
    protected bool $autorun = false;
    protected ?Closure $fakeHandler = null;

    private int $callsCount = 0;

    public function override(Closure $handler): static
    {
        $this->fakeHandler = $handler;
        return $this->autorun();
    }

    public function disable(): static
    {
        return $this->override(function() {});
    }

    public function autorun(): static
    {
        $this->autorun = true;
        return $this;
    }

    public function getCallsCount(): int
    {
        return $this->callsCount;
    }

    protected function addCall(): void
    {
        $this->callsCount += 1;
    }
}
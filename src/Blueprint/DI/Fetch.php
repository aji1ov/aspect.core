<?php

namespace Aspect\Lib\Blueprint\DI;

#[\Attribute(\Attribute::TARGET_PROPERTY, \Attribute::TARGET_PARAMETER)]
class Fetch
{
    private ?string $name;
    private bool $lazy;
    public function __construct(?string $name = null, bool $lazy = false) {
        $this->name = $name;
        $this->lazy = $lazy;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isLazy(): bool
    {
        return $this->lazy;
    }
}
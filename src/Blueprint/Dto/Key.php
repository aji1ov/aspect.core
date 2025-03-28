<?php

namespace Aspect\Lib\Blueprint\Dto;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Key
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
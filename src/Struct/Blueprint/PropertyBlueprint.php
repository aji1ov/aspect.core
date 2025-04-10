<?php

namespace Aspect\Lib\Struct\Blueprint;

class PropertyBlueprint
{
    private \ReflectionProperty $property;
    private array $attributes = [];

    public function __construct(\ReflectionProperty $property) {
        $this->property = $property;
    }
}
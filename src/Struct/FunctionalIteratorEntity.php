<?php

namespace Aspect\Lib\Struct;

class FunctionalIteratorEntity
{
    private mixed $key;
    private mixed $value;
    public function __construct(mixed $key, mixed $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getKey(): mixed
    {
        return $this->key;
    }
}
<?php

namespace Aspect\Lib\Service\Console;

enum Background: int
{
    case RED = 41;
    case LIGHT_RED = 101;
    case DEFAULT  = 49;

    public function make(): string
    {
        return "\e[".$this->value."m";
    }

    public function wrap(string $inner): string
    {
        return $this->make().$inner.Background::DEFAULT->make();
    }
}
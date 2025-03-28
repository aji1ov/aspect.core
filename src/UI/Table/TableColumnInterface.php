<?php

namespace Aspect\Lib\UI\Table;

interface TableColumnInterface
{
    public static function make(string $key): static;
}
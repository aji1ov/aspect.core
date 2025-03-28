<?php

namespace Aspect\Lib\Transport\Blueprint;

interface CompatiblePropertyInterface
{
    public function compatible(mixed $source): bool;
}
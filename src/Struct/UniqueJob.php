<?php

namespace Aspect\Lib\Struct;

trait UniqueJob
{
    public function getSign(): string
    {
        return md5(static::class);
    }
}
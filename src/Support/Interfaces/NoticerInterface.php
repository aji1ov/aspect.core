<?php

namespace Aspect\Lib\Support\Interfaces;

interface NoticerInterface
{
    public function notice(mixed ...$arguments): void;
}
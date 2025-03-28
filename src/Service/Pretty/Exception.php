<?php

namespace Aspect\Lib\Service\Pretty;

interface Exception
{
    public function makePretty(\Throwable $throwable): string;
}
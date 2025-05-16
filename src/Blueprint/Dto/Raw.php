<?php

namespace Aspect\Lib\Blueprint\Dto;

use Aspect\Lib\Transport\Blueprint\RawConverter;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Raw extends Convert
{
    public function __construct()
    {
        parent::__construct(new RawConverter());
    }
}
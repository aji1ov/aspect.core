<?php

namespace Aspect\Lib\Service\Console;

enum Argument: int
{
    case REQUIRED = 1;
    case OPTIONAL = 2;
    case REQUIRED_ARRAY = 5;
    case OPTIONAL_ARRAY = 6;
}

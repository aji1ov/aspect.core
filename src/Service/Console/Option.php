<?php

namespace Aspect\Lib\Service\Console;

enum Option: int
{
    case NONE = 1;
    case REQUIRED = 2;
    case OPTIONAL = 4;
    case OPTIONAL_ARRAY = 12;
    case REQUIRED_ARRAY = 10;
}
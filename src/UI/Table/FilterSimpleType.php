<?php

namespace Aspect\Lib\UI\Table;

enum FilterSimpleType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case CHECKBOX = 'checkbox';
}
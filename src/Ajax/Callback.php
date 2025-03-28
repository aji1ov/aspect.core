<?php

namespace Aspect\Lib\Ajax;

abstract class Callback
{
    abstract function toJavaScript(): string;
}
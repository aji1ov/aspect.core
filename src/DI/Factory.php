<?php

namespace Aspect\Lib\DI;

use Aspect\Lib\DI\Factory\MakeFactory;
use Aspect\Lib\DI\Factory\SingletonFactory;

abstract class Factory
{
    abstract public function make(Container $container);
}

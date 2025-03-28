<?php

namespace Aspect\Lib\DI;

use Aspect\Lib\Application;
use Aspect\Lib\DI\Invoke\ObjectBuilder;
use Aspect\Lib\DI\Serializer\ObjectSerializer;

abstract class Serializable
{
    public function __sleep()
    {
        return ObjectSerializer::getSerializedProperties($this);
    }

    /**
     * @throws \Exception
     */
    public function __wakeup()
    {
        Application::getInstance()->fetchTo($this);
    }
}
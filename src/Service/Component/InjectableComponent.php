<?php

namespace Aspect\Lib\Service\Component;

use Aspect\Lib\Application;
use CBitrixComponent;

class InjectableComponent extends CBitrixComponent
{
    public function __construct($component = null)
    {
        Application::getInstance()->fetchTo($this);
        parent::__construct($component);
    }
}
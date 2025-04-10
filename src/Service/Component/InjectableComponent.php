<?php

namespace Aspect\Lib\Service\Component;

use Aspect\Lib\Application;
use CBitrixComponent;
use Exception;

class InjectableComponent extends CBitrixComponent
{
    /**
     * @throws Exception
     */
    public function __construct($component = null)
    {
        Application::getInstance()->fetchTo($this);
        parent::__construct($component);
    }
}
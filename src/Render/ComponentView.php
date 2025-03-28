<?php

namespace Aspect\Lib\Render;

use Aspect\Lib\Support\Interfaces\ViewInterface;

abstract class ComponentView implements ViewInterface
{
    private mixed $componentResult = null;

    public final function render(): string
    {
        global $APPLICATION;
        ob_start();
        $this->componentResult = $APPLICATION->IncludeComponent($this->getComponentName(), $this->getComponentTemplate(), $this->getComponentParams());
        return ob_get_clean();
    }

    abstract protected function getComponentName(): string;
    abstract protected function getComponentParams(): array;

    protected function getComponentTemplate(): string
    {
        return "";
    }

    public function getComponentResult(): mixed
    {
        return $this->componentResult;
    }
}
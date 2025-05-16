<?php

namespace Aspect\Lib\Service\Pretty\Exception;

use Aspect\Lib\Render\ComponentView;
use Aspect\Lib\Service\Pretty\Exception;

class Component extends ComponentView implements Exception
{

    private ?\Throwable $throwable = null;

    public function makePretty(\Throwable $throwable): string
    {
        $this->throwable = $throwable;
        $render = $this->render();
        $this->throwable = null;
        return $render;
    }

    protected function getComponentName(): string
    {
        return 'aspect:pretty.exception';
    }

    protected function getComponentParams(): array
    {
        return [
            'EXCEPTION' => $this->throwable
        ];
    }
}
<?php

namespace Aspect\Lib\UI\Table;

use Aspect\Lib\Ajax\Callback;
use Aspect\Lib\Render\ComponentView;
use Closure;

class Button extends ComponentView implements ButtonInterface
{
    private string $title;
    private string $color;
    private ?Closure $handler = null;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public static function make(string $title): Button
    {
        return new Button($title);
    }

    public function color(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function primary(): static
    {
        return $this->color('primary');
    }

    public function success(): static
    {
        return $this->color('success');
    }

    public function danger(): static
    {
        return $this->color('danger');
    }

    public function ai(): static
    {
        return $this->color('color-ai');
    }

    public function warning(): static
    {
        return $this->color('warning-light');
    }

    public function onClick(Closure $handler): static
    {
        $this->handler = $handler;
        return $this;
    }

    protected function getComponentName(): string
    {
        return "aspect:ui.button";
    }

    protected function getComponentParams(): array
    {
        $onClickHandler = null;

        if ($this->handler) {
            $handler = $this->handler;
            $handlerFunction = $handler();
            if ($handlerFunction instanceof Callback) {
                $onClickHandler = $handlerFunction->toJavaScript();
            }
        }

        return [
            'TITLE' => $this->title,
            'COLOR' => $this->color,
            'HANDLER' => $onClickHandler
        ];
    }
}
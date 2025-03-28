<?php

namespace Aspect\Lib\Ajax;

class JsExpression extends DependedExpression
{
    private string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    public static function call($expression): static
    {
        return new static($expression);
    }

    protected function getExpression(): string
    {
        return $this->expression;
    }

    function toJavaScript(): string
    {
        return $this->getExpression();
    }
}
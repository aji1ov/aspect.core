<?php

namespace Aspect\Lib\Event;

abstract class EventSource
{
    protected bool $resultMustReplaced = false;
    public function __construct(bool $resultMustReplaced)
    {
        $this->resultMustReplaced = $resultMustReplaced;
    }

    public function isResultMustReplaced(): bool
    {
        return $this->resultMustReplaced;
    }

    abstract public function getResult(): mixed;

}
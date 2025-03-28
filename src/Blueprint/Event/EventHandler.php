<?php

namespace Aspect\Lib\Blueprint\Event;

use Aspect\Lib\Event\EventSource;

#[\Attribute(\Attribute::TARGET_METHOD)]
abstract class EventHandler
{

    private ?string $eventName;

    public function __construct(?string $eventName = null)
    {
        $this->eventName = $eventName;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    abstract public function makeEventSource(array $input): EventSource;
    abstract public function submitEventSource(EventSource $source, array &...$input): void;
    abstract public function spreadSelf(EventSource $source, array $argumentMap): array;
}
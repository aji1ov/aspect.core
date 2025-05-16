<?php

namespace Aspect\Lib\Blueprint\Event;

use Aspect\Lib\Event\EventSource;
use Aspect\Lib\Event\ListEventSource;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ArrayEvent extends EventHandler
{

    private array $takeTo;

    public function __construct(?string $eventName = null, array $takeTo = ['fields'])
    {
        $this->takeTo = $takeTo;
        parent::__construct($eventName);
    }

    public function makeEventSource(array $input): EventSource
    {
        $newData = [];
        foreach ($input as $key => $values)
        {
            $newData[$this->takeTo[$key]] = $values;
        }
        return new ListEventSource($newData);
    }

    public function submitEventSource(EventSource $source, mixed &...$input): void
    {
        $result = $source->getResult();
        foreach ($input as $key => $value) {
            $input[$key] = $result[$this->takeTo[$key]];
        }
    }

    private function withSourceMap(EventSource $source, array &$argumentMap): array
    {
        $withSourceMap = [];

        foreach ($argumentMap as $argumentKey => $argumentTypes) {
            if (in_array(EventSource::class, $argumentTypes, true) || in_array(ListEventSource::class, $argumentTypes, true)) {
                $withSourceMap[$argumentKey] = $source;
                unset($argumentMap[$argumentKey]);
            }
        }

        return $withSourceMap;
    }

    public function spreadSelf(EventSource $source, array $argumentMap): array
    {
        $spreadList = [];

        $spreadList = array_merge($spreadList, $this->withSourceMap($source, $argumentMap));

        return $spreadList;
    }
}
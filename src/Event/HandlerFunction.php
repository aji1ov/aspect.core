<?php

namespace Aspect\Lib\Event;

use Aspect\Lib\Application;
use Aspect\Lib\Blueprint\Event\EventHandler;
use Aspect\Lib\DI\Invoke\FunctionInvoker;
use Aspect\Lib\Transport\TransportInterface;
use Bitrix\Main\EventManager;

class HandlerFunction
{

    private string $fromModuleName;
    private string $fromEventName;

    private \ReflectionMethod $eventFunction;

    private EventHandler $handler;

    public function __construct(EventHandler $handler, \ReflectionMethod $eventFunction)
    {
        $this->eventFunction = $eventFunction;

        $functionClassNamespace = explode("\\", $this->eventFunction->getDeclaringClass()->getName());
        $this->fromModuleName = strtolower(array_pop($functionClassNamespace));

        $this->fromEventName = $handler->getEventName() ?? $this->eventFunction->getName();

        $this->handler = $handler;
    }

    public function subscribe(EventManager $manager): void
    {
        $manager->addEventHandler($this->fromModuleName, $this->fromEventName, function (mixed &... $eventData) {
            $eventSource = $this->handler->makeEventSource($eventData);

            $eventPackage = Application::getInstance()->get($this->eventFunction->getDeclaringClass()->getName());
            assert($eventPackage instanceof EventPackage);

            $invokeWith = $this->handler->spreadSelf($eventSource, $this->getArgumentMap());

            Application::getInstance()->injectMethod($this->eventFunction, $eventPackage, $invokeWith);

            if ($eventSource->isResultMustReplaced()) {
                $this->handler->submitEventSource($eventSource, ...$eventData);
            }
        });
    }

    /**
     * @throws \Exception
     */
    private function getArgumentMap(): array
    {
        $map = [];

        foreach ($this->eventFunction->getParameters() as $parameter) {
            foreach ($this->prepareType($parameter->getType()) as $type) {
                if(
                    is_a($type, EventSource::class, true)
                    || is_a($type, TransportInterface::class, true)) {
                    $map[$parameter->getName()][] = $type;
                }
            }
        }

        return $map;
    }

    private function prepareType(\ReflectionType $type): array
    {
        $preparedTypes = [];

        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                $preparedTypes = array_merge($preparedTypes, $this->prepareType($unionType));
            }
        } else if ($type instanceof \ReflectionNamedType) {
            $preparedTypes[] = $type->getName();
        }

        return array_unique($preparedTypes);
    }
}
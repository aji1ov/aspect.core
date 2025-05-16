<?php

namespace Aspect\Lib\Event;

use Aspect\Lib\Blueprint\Event\EventHandler;
use Aspect\Lib\Facade\Yakov;
use Aspect\Lib\Helper\ClassLoader;
use Bitrix\Main\EventManager;
use ReflectionException;

class Repository
{
    /**
     * @throws ReflectionException
     */
    public function makeEvents(): void
    {
        $handlerFunctions = [];
        foreach ($this->getPackages() as $package) {
            $handlerFunctions[] = $this->getHandlerFunctions($package);
        }

        $eventManager = EventManager::getInstance();
        foreach (array_merge(...$handlerFunctions) as $handlerFunction) {
            $handlerFunction->subscribe($eventManager);
        }

    }

    /**
     * @param string $eventPackage
     * @return HandlerFunction[]
     * @throws ReflectionException
     */
    public function getHandlerFunctions(string $eventPackage): array
    {
        $handlerFunctions = [];
        $rc = new \ReflectionClass($eventPackage);
        foreach ($rc->getMethods() as $method) {
            if ($handlerAttributes = $method->getAttributes(EventHandler::class, \ReflectionAttribute::IS_INSTANCEOF)) {
                foreach ($handlerAttributes as $handlerAttribute) {
                    $handler = $handlerAttribute->newInstance();
                    assert($handler instanceof EventHandler);

                    $handlerFunctions[] = new HandlerFunction($handler, $method);
                }
            }
        }

        return $handlerFunctions;
    }

    public function getPackages(): array
    {
        return (new ClassLoader())->getClassesFromYakov(
            Yakov::getPathToEvents(),
            fn ($className) => is_a($className, EventPackage::class, true)
        );
    }
}
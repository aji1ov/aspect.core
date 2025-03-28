<?php

namespace Aspect\Lib\PhpUnit;

use Aspect\Lib\Application;
use Aspect\Lib\PhpUnit\Faker\LegacyFaker;
use Aspect\Lib\Service\Background\Dispatcher\FakeJobDispatcher;
use Aspect\Lib\Service\Console\FakeCommandDispatcher;
use Aspect\Lib\Struct\Singleton;
use Aspect\Lib\Support\Interfaces\CommandDispatcherInterface;
use Aspect\Lib\Support\Interfaces\JobDispatcherInterface;

class Faker
{

    use Singleton;

    /**
     * @var \Closure[]
     */
    private array $onCloseHandlers = [];

    public function legacy(): LegacyFaker
    {
        return LegacyFaker::getInstance();
    }

    public function commands(): FakeCommandDispatcher
    {
        Application::getInstance()->fake()->add(CommandDispatcherInterface::class, FakeCommandDispatcher::class);
        return Application::getInstance()->get(CommandDispatcherInterface::class);
    }

    public function queue(): FakeJobDispatcher
    {
        Application::getInstance()->fake()->add(JobDispatcherInterface::class, FakeJobDispatcher::class);
        return Application::getInstance()->get(JobDispatcherInterface::class);
    }

    public function defer(\Closure $onCloseHandler): static
    {
        $this->onCloseHandlers[] = $onCloseHandler;
        return $this;
    }

    public function close(): void
    {
        $onCloseHandlers = $this->onCloseHandlers;
        $this->onCloseHandlers = [];
        foreach ($onCloseHandlers as $handler)
        {
            $handler();
        }
    }
}
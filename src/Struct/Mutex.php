<?php

namespace Aspect\Lib\Struct;

use Aspect\Lib\Application;
use Aspect\Lib\Blueprint\DI\Fetch;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

trait Mutex
{

    #[Fetch]
    protected LockFactory $factory;

    protected function withWaitedLock(callable $closure): mixed
    {
        return $this->withMutex($this->getMutex(), true, $closure);
    }

    private function withMutex(LockInterface $lock, bool $wait, callable $closure): mixed
    {
        $lock->acquire($wait);
        $result = Application::getInstance()->inject($closure);
        $lock->release();

        return $result;
    }

    private function getMutex(): LockInterface
    {
        return $this->factory->createLock($this->getMutexResource());
    }

    protected function getMutexResource(): string
    {
        return static::class;
    }
}
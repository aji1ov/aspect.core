<?php

namespace Aspect\Lib\Manager;

use Aspect\Lib\Repository\ScriptTable;
use Aspect\Lib\Struct\Mutex;
use Aspect\Lib\Struct\ServiceLocator;

class ScriptManager
{
    use Mutex;
    use ServiceLocator;

    public const SCRIPT_NAME = 'NAME';
    public const SCRIPT_OUTPUT = 'OUTPUT';
    public const RUNNING_FLAG = 'RUNNING';
    public const CANCELLED_FLAG = 'CANCELLED';

    public function isRunning(string $name): bool
    {
        return !!ScriptTable::getCount([static::SCRIPT_NAME => $name, static::RUNNING_FLAG => true]);
    }

    public function isCanceled(string $name): bool
    {
        return !!ScriptTable::getCount([static::SCRIPT_NAME => $name, static::CANCELLED_FLAG => true]);
    }

    public function index(string $name): int
    {
        return ScriptTable::add([
            static::SCRIPT_NAME => $name,
            static::RUNNING_FLAG => false,
            static::CANCELLED_FLAG => false
        ])->getId();
    }

    public function create(string $name): int
    {
        return ScriptTable::add([
            static::SCRIPT_NAME => $name,
            static::RUNNING_FLAG => true,
            static::CANCELLED_FLAG => false
        ])->getId();
    }

    public function clear(string $name): void
    {
        $iterator = ScriptTable::getList(['filter' => [static::SCRIPT_NAME => $name]])->getIterator();
        foreach ($iterator as $script) {
            ScriptTable::delete($script['ID']);
        }
    }

    public function register(string $name): ?int
    {
        return $this->withWaitedLock(function () use ($name) {
            if ($this->isRunning($name)) {
                return null;
            }

            $this->clear($name);
            return $this->create($name);
        });
    }

    public function finish(string $name): void
    {
        $iterator = ScriptTable::getList(['filter' => [static::SCRIPT_NAME => $name]])->getIterator();
        foreach ($iterator as $script) {
            ScriptTable::update($script['ID'], [static::RUNNING_FLAG => false]);
        }
    }

    public function cancel(string $name): void
    {
        $iterator = ScriptTable::getList(['filter' => [static::SCRIPT_NAME => $name, static::RUNNING_FLAG => true]])->getIterator();
        foreach ($iterator as $script) {
            ScriptTable::update($script['ID'], [static::CANCELLED_FLAG => true]);
        }
    }

    public function addOutput(string $name, string $output): void
    {
        $iterator = ScriptTable::getList(['filter' => [static::SCRIPT_NAME => $name, static::RUNNING_FLAG => true]])->getIterator();
        foreach ($iterator as $script) {
            ScriptTable::update($script['ID'], [static::SCRIPT_OUTPUT => $script[static::SCRIPT_OUTPUT] . $output]);
        }
    }

    public function getOutput(string $name): ?string
    {
        $iterator = ScriptTable::getList(['filter' => [static::SCRIPT_NAME => $name]])->getIterator();
        foreach ($iterator as $script) {
            return $script[static::SCRIPT_OUTPUT];
        }

        return null;
    }

    public function get(string $name): ?array
    {
        $iterator = ScriptTable::getList(['filter' => [static::SCRIPT_NAME => $name]])->getIterator();
        foreach ($iterator as $script) {
            return $script;
        }

        return null;
    }
}
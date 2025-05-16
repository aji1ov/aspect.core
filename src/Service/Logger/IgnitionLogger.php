<?php

namespace Aspect\Lib\Service\Logger;

use Aspect\Lib\Struct\Singleton;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\HandlerWrapper;
use Monolog\LogRecord;
use Spatie\FlareClient\Enums\MessageLevels;
use Spatie\FlareClient\Flare;

class IgnitionLogger implements HandlerInterface
{
    use Singleton;

    /**
     * @var LogRecord[] $buffer
     */
    private array $buffer = [];
    private Flare $flare;

    public function setFlare(Flare $flare): void
    {
        $this->flare = $flare;
    }

    public function isHandling(LogRecord $record): bool
    {
        return true;
    }

    public function handle(LogRecord $record): bool
    {
        $this?->flare->glow(
            $record->message,
            strtolower($record->level->getName()),
            array_merge($record->context, $record->extra)
        );
        return true;
    }

    public function handleBatch(array $records): void
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    public function close(): void
    {
    }
}
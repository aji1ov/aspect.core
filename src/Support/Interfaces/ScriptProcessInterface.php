<?php

namespace Aspect\Lib\Support\Interfaces;

interface ScriptProcessInterface
{
    public function start(string $command): bool;
    public function close(): void;
    public function kill(): void;
    public function isRunning(): bool;
    public function readAvailableOutput(): ?string;
}
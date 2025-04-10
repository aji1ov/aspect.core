<?php

namespace Aspect\Lib\Service\ScriptProcess;

use Aspect\Lib\Support\Interfaces\ScriptProcessInterface;

class ProcOpenScriptProcess implements ScriptProcessInterface
{
    private $process;
    private array $pipes;
    private int $processId;

    public const PIPE_IN = 0;
    public const PIPE_OUT = 1;
    public const PIPE_ERR = 2;

    public function start(string $command): bool
    {
        $started = false;
        $descriptors = [
            static::PIPE_IN => ["pipe", "r"],
            static::PIPE_OUT => ["pipe", "w"],
            static::PIPE_ERR => ["pipe", "w"],
        ];

        $this->process = proc_open($command, $descriptors, $pipes);
        if (is_resource($this->process)) {
            $started = true;
            $this->pipes = $pipes;

            $status = proc_get_status($this->process);
            $this->processId = $status['pid'];

            stream_set_blocking($pipes[1], 0);
            stream_set_blocking($pipes[2], 0);
        }

        return $started;
    }

    public function kill(): void
    {
        exec("kill -9 ".$this->processId);
    }

    public function isRunning(): bool
    {
        $status = proc_get_status($this->process);
        return $status['running'];
    }

    public function readAvailableOutput(): ?string
    {
        return stream_get_contents($this->pipes[static::PIPE_OUT]).stream_get_contents($this->pipes[static::PIPE_ERR]);
    }

    public function close(): void
    {
        proc_close($this->process);
    }
}
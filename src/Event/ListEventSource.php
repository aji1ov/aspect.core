<?php

namespace Aspect\Lib\Event;

class ListEventSource extends EventSource
{

    private array $input;

    public function __construct(array $input)
    {
        $this->input = $input;
        parent::__construct(true);
    }

    public function getInput(): array
    {
        return $this->input;
    }

    public function getValue(string $key): mixed
    {
        return $this->input[$key];
    }

    public function setOutput(string $key, mixed $value): void
    {
        $this->input[$key] = $value;
    }

    public function newResult(array $output): void
    {
        $this->input = $output;
    }

    public function getResult(): array
    {
        return $this->getInput();
    }
}
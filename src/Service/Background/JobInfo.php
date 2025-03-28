<?php

namespace Aspect\Lib\Service\Background;

class JobInfo
{
    protected string $name;
    protected string $queue;
    protected string $sign;
    protected bool $isBusy;
    protected int $startAt;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function setQueue(string $queue): void
    {
        $this->queue = $queue;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }

    public function isBusy(): bool
    {
        return $this->isBusy;
    }

    public function setIsBusy(bool $isBusy): void
    {
        $this->isBusy = $isBusy;
    }

    public function getStartAt(): int
    {
        return $this->startAt;
    }

    public function setStartAt(int $startAt): void
    {
        $this->startAt = $startAt;
    }
}
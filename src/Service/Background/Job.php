<?php

namespace Aspect\Lib\Service\Background;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Blueprint\Ignore;
use Aspect\Lib\DI\Serializable;
use Psr\Log\LoggerInterface;

abstract class Job extends Serializable
{
    #[Fetch]
    protected LoggerInterface $logger;

    #[Ignore]
    protected int $plannedAt;

    #[Ignore]
    protected int $startedAt;

    #[Ignore]
    protected bool $checkUnique = false;

    #[Ignore]
    protected int $id;

    public static function unserialize(int $id, string $serial, int $plannedAt): Job
    {
        $job = unserialize($serial, ['allowed_classes' => true]);
        assert(is_a($job, __CLASS__, true));

        $job->id = $id;
        $job->plannedAt = $plannedAt;
        $job->startedAt = time();

        return $job;
    }

    abstract public function handle(): void;

    public function getPlannedAt(): int
    {
        return $this->plannedAt;
    }

    public function getStartedAt(): int
    {
        return $this->startedAt;
    }

    public function getName(): string
    {
        return 'Job<' . static::class . '::class>';
    }

    public function getSign(): string
    {
        return md5($this->serialize());
    }

    public function serialize(): string
    {
        return serialize($this);
    }

    public function checkUnique(): bool
    {
        return $this->checkUnique;
    }

    public function setUnique(bool $isUnique): void
    {
        $this->checkUnique = $isUnique;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
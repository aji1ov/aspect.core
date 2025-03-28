<?php

namespace Aspect\Lib\Support\Interfaces;

use Aspect\Lib\Service\Background\Job;

interface JobProviderInterface
{
    public function has(int $startAt): bool;
    public function next(int $startAt): ?Job;
    public function remove(int $id): void;
    public function freedom(int $id): void;

    public function handle(Job $job): bool;
}
<?php

namespace Aspect\Lib\Service\Background\Dispatcher;

use Aspect\Lib\DI\FakeInterface;
use Aspect\Lib\PhpUnit\Faker;
use Aspect\Lib\PhpUnit\Faker\Exception\DisallowedFakerException;
use Aspect\Lib\PhpUnit\Faker\Exception\ExpectedFakerException;
use Aspect\Lib\PhpUnit\Faker\FakerRule;
use Aspect\Lib\PhpUnit\Faker\QueueFakerRule;
use Aspect\Lib\Service\Background\Job;
use Aspect\Lib\Support\Interfaces\JobDispatcherInterface;
use Aspect\Lib\Support\Interfaces\JobProviderInterface;
use Protobuf\Exception;

class FakeJobDispatcher implements JobDispatcherInterface, FakeInterface
{
    private array $fakeQueue = [];

    /**
     * @var QueueFakerRule[]
     */
    private array $rules = [];
    private array $disallowed = [];

    public function __construct()
    {
        Faker::getInstance()->defer(function() {
            if (!empty($this->disallowed)) {
                throw new DisallowedFakerException('Added unexpected jobs: ' . implode(", ", $this->disallowed));
            }
        });
    }

    /**
     * @param class-string<Job> $job
     */
    public function allow(string $job): FakerRule
    {
        $rule = new QueueFakerRule($job);
        $this->rules[$job] = $rule;
        return $rule;
    }

    /**
     * @param class-string<Job> $name
     */
    public function expect(string $name): FakerRule
    {
        Faker::getInstance()->defer(function() use ($name){
            $this->assertCalls($name);
        });
        return $this->allow($name);
    }

    public function dispatch(Job $job, string $queue, int $startAt): void
    {
        if ($rule = $this->rules[get_class($job)]) {
            $rule->handle($job);
        } else {
            $this->disallowed[] = get_class($job);
        }
    }

    public function isDefined(Job $job, string $queue): bool
    {
        return isset($this->fakeQueue[get_class($job)]);
    }

    /**
     * @throws Exception
     */
    public function getProvider(?array $queues): JobProviderInterface
    {
        throw new Exception("Not implemeted");
    }

    public function getInfo(?array $queues): array
    {
        return [];
    }

    /**
     * @param class-string<Job> $job
     * @throws ExpectedFakerException
     */
    public function assertCalls(string $job): void
    {
        if ($rule = $this->rules[$job])
        {
            if (!$rule->getCallsCount()) {
                throw new ExpectedFakerException('Expected command('.$job.') not added');
            }
        }
    }

    /**
     * @param class-string<Job> $job
     */
    public function calls(string $job): int {
        if ($rule = $this->rules[$job])
        {
            return $rule->getCallsCount();
        }

        return 0;
    }
}
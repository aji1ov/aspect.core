<?php

namespace Aspect\Lib\PhpUnit\Faker;

use Aspect\Lib\Service\Background\Job;
use Closure;

class QueueFakerRule extends FakerRule
{
    /** @var class-string<Job>  */
    private string $job;
    /**
     * @param class-string<Job> $job
     */
    public function __construct(string $job)
    {
        $this->job = $job;
    }

    public function handle(Job $job): void
    {
        $this->addCall();
        if ($this->autorun) {
           $handler = $job->handle(...);

           if($this->fakeHandler) {
               $handler = $this->fakeHandler;
           }

           $handler();
        }
    }

}
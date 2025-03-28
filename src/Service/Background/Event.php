<?php

namespace Aspect\Lib\Service\Background;

use Aspect\Lib\Blueprint\Ignore;
use Cron\CronExpression;

class Event
{
    #[Ignore]
    protected Job $job;

    protected string $description;

    protected ?CronExpression $expression = null;

    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->description = $job->getName();
    }

    public function period(string $expression): static
    {
        $this->expression = new CronExpression($expression);
        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        if($this->job instanceof  ClosureJob) {
            $this->job->setName($description);
        }
        return $this;
    }

    public static function sign(Event $event): string
    {
        return md5(implode(".", [
            $event->job->getSign(),
            $event->expression->getExpression(),
            $event->description
        ]));
    }

    /**
     * @throws \Exception
     */
    public function getCheckTime(int $now): int
    {
        return $this->expression->getNextRunDate($now)->getTimestamp();
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getExpression(): ?CronExpression
    {
        return $this->expression;
    }


}
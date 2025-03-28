<?php

namespace Aspect\Lib\PhpUnit\Faker\Dto;

use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Transport\Dto;

class AddAgentParametersDto extends Dto
{
    public string $name;
    public string $module;
    public string $period;
    public int $interval;

    #[Key('datecheck')]
    public string $dateCheck;
    public string $active;

    #[Key('next_exec')]
    public string $nextExec;
    public int $sort;

    #[Key('user_id')]
    public ?int $userId;
    public bool $existError;
}
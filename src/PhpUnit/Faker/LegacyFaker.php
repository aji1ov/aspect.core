<?php

namespace Aspect\Lib\PhpUnit\Faker;

use Aspect\Lib\Struct\Singleton;

class LegacyFaker
{

    use Singleton;

    public function agents(): AgentFaker
    {
        return AgentFaker::getInstance();
    }
}
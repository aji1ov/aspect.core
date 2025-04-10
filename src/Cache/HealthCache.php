<?php

namespace Aspect\Lib\Cache;

use Aspect\Lib\Service\Cache\CacheContainer;

class HealthCache extends CacheContainer
{
    protected function dir(): string
    {
        return "/aspect/lib/background/health";
    }

    public function ttl(): int
    {
        return 3600;
    }
}
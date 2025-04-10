<?php

namespace Aspect\Lib\Preset\Background;

use Aspect\Lib\Cache\HealthCache;
use Aspect\Lib\Service\Background\Job;
use Carbon\Carbon;

class HealthJob extends Job
{

    public function handle(): void
    {
        HealthCache::getInstance()->set(static::class, now()->unix());
    }

    public static function check(): bool
    {
        $cache = HealthCache::getInstance();
        return (int) $cache->get(static::class, 0) + $cache->ttl() > now()->unix();
    }

    public static function isCacheOutdated(): bool
    {
        return HealthCache::getInstance()->has(static::class);
    }
}
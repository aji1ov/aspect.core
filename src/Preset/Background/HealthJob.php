<?php

namespace Aspect\Lib\Preset\Background;

use Aspect\Lib\Service\Background\Job;
use Bitrix\Main\Data\Cache;

class HealthJob extends Job
{
    const TTL = 3600;
    const DIR = '/aspect/lib/background/health/';
    const KEY = 'result';

    private static function cache(): Cache
    {
        $cache = Cache::createInstance();
        $cache->initCache(static::TTL, static::class, static::DIR);

        return $cache;
    }

    public function handle(): void
    {
        $cache = static::cache();
        $cache->startDataCache(static::TTL, static::class, static::DIR);
        $cache->endDataCache([
            static::KEY => now()->unix()
        ]);
    }

    public static function check(): bool
    {
        $lastRun = static::cache()->getVars()[static::KEY] ?? 0;
        return $lastRun + static::TTL > now()->unix();
    }
}
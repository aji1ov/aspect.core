<?php

namespace Aspect\Lib\Facade;

use Aspect\Lib\Service\Cache\CacheProvider;
use Aspect\Lib\Support\Interfaces\CacheInterface;
use Bitrix\Main\Application;

class Cache
{
    public static function section(int $ttl = 3600, ?string $section = null): CacheInterface
    {
        return new CacheProvider($ttl, $section);
    }

    public static function get(string $key, mixed $defaultValue, bool $store = true, int $ttl = 3600, ?string $section = null): mixed
    {
        return static::section($ttl, $section)->get($key, $defaultValue, $store);
    }

    public static function delete(string $key, int $ttl = 3600, ?string $section = null): true
    {
        return static::section($ttl, $section)->delete($key);
    }

    public static function set(string $key, mixed $value, int $ttl = 3600, ?string $section = null): void
    {
        static::section($ttl, $section)->set($key, $value);
    }

    public static function has(string $key, int $ttl = 3600, ?string $section = null): bool
    {
        return static::section($ttl, $section)->has($key);
    }

    public static function clearTags(string ...$tags): void
    {
        $cache = Application::getInstance()->getTaggedCache();

        foreach ($tags as $tag) {
            $cache->clearByTag($tag);
        }
    }
}
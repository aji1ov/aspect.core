<?php

namespace Aspect\Lib\Service\Cache;

use Aspect\Lib\Support\Interfaces\CacheInterface;
use Bitrix\Main\Application;
use Bitrix\Main\Data\ManagedCache;

class CacheProvider implements CacheInterface
{
    private ManagedCache $cache;

    //private string $key;
    private int $ttl;
    private ?string $section = null;

    public function __construct(int $ttl = 3600, ?string $section = null)
    {
        $this->cache = Application::getInstance()->getManagedCache();

        //$this->key = $key;
        $this->ttl = $ttl;
        $this->section = $section;
    }

    public function get(string $key, mixed $defaultValue = null, bool $store = true): mixed
    {
        if ($this->has($key)) {
            return $this->cache->get($key);
        }

        if ($defaultValue) {

            $value = $this->createValue($defaultValue);

            if ($store && $value) {
                $this->addItem($key, $value);
            }

            return $value;
        }

        return null;
    }

    private function addItem(string $key, mixed $value): void
    {
        $this->cache->setImmediate($key, $value);
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function createValue(mixed $value, ?CacheSlot $slot = null): mixed
    {
        if(!$slot) {
            $slot = new CacheSlot($this);
        }

        if (is_callable($value)) {
            $value = \Aspect\Lib\Application::getInstance()->inject($value, ['cache' => $slot]);
        }

        return $value;
    }

    public function delete(string $key): true
    {
        $this->cache->clean($key, $this->section);
        return true;
    }

    public function set(string $key, mixed $value): void
    {
        if ($this->cache->read($this->ttl, $key, $this->section)) {
            $this->cache->clean($key, $this->section);
            $this->cache->read($this->ttl, $key, $this->section);
        }
        $saveValue = $this->createValue($value);

        if (isset($saveValue)) {
            $this->addItem($key, $saveValue);
        }
    }

    public function has(string $key): bool
    {
        return (bool)$this->cache->read($this->ttl, $key, $this->section);
    }

    public function deleteAll(): true
    {
        if($section = $this->section) {
            $this->cache->cleanDir($section);
        } else {
            $this->cache->cleanAll();
        }

        return true;
    }
}
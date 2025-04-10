<?php

namespace Aspect\Lib\Service\Cache;

use Aspect\Lib\Facade\Cache;
use Aspect\Lib\Struct\ServiceLocator;
use Aspect\Lib\Support\Interfaces\CacheInterface;
use ReflectionClass;

abstract class CacheContainer implements CacheInterface
{
    use ServiceLocator;

    private CacheProvider $cacheProvider;

    public function __construct()
    {
        $this->cacheProvider = new CacheProvider($this->ttl(), $this->dir());
    }

    abstract protected function dir(): string;
    abstract public function ttl(): int;

    /**
     * @return string[]
     */
    protected function tags(): array
    {
        return [];
    }

    public function getTags(): array
    {
        $self = new ReflectionClass(static::class);

        $tags = [
            $this->tags(),
            [$self->getShortName()]
        ];

        if (($parent = $self->getParentClass()) && $parent->getName() !== __CLASS__) {
            $tags[] = $parent->getMethod('getTags')->invoke($parent->newInstance());
        }

        return array_unique(array_merge(...$tags));
    }

    public function get(string $key, mixed $defaultValue = null, bool $store = true): mixed
    {
        return $this->cacheProvider->get($key, function(CacheSlot $slot) use ($defaultValue) {
            foreach ($this->getTags() as $tag) {
                $slot->tag($tag);
            }
            return $this->cacheProvider->createValue($defaultValue, $slot);
        }, $store);
    }

    public function delete(string $key): true
    {
        return $this->cacheProvider->delete($key);
    }

    public function set(string $key, mixed $value): void
    {
        $this->cacheProvider->set($key, $value);
    }

    public function has(string $key): bool
    {
        return $this->cacheProvider->has($key);
    }

    public function clearTags(): void
    {
        Cache::clearTags(...$this->getTags());
    }

    public function deleteAll(): true
    {
        $this->clearTags();
        return $this->cacheProvider->deleteAll();
    }
}


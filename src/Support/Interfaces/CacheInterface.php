<?php

namespace Aspect\Lib\Support\Interfaces;

use Closure;

interface CacheInterface
{
    public function get(string $key, mixed $defaultValue = null, bool $store = true): mixed;
    public function delete(string $key): true;
    public function set(string $key, mixed $value): void;
    public function has(string $key): bool;

    public function deleteAll(): true;

}
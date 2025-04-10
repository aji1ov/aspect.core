<?php

namespace Aspect\Lib\Service\Cache;

use Bitrix\Main\Application;
use Bitrix\Main\Data\TaggedCache;

class CacheSlot
{
    private CacheProvider $provider;
    private ?TaggedCache $taggedCache = null;

    public function __construct(CacheProvider $provider)
    {
        $this->provider = $provider;
    }

    private function tagged(): TaggedCache
    {
        if (!$this->taggedCache) {
            $this->taggedCache = Application::getInstance()->getTaggedCache();
            $this->taggedCache->startTagCache($this->provider->getSection());
        }

        return $this->taggedCache;
    }

    public function tag(string $name): void
    {
        $this->tagged()->registerTag($name);
    }

    public function abort(): true
    {
        $this->taggedCache?->abortTagCache();
        return true;
    }

}
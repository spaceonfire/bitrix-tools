<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

use Traversable;

abstract class AbstractCacheMapDecorator implements CacheMap
{
    /**
     * @var CacheMap
     */
    private $cacheMap;

    /**
     * AbstractCacheMapDecorator constructor.
     * @param CacheMap $cacheMap
     */
    public function __construct(CacheMap $cacheMap)
    {
        $this->cacheMap = $cacheMap;
    }

    /**
     * @inheritDoc
     */
    public function get($code)
    {
        return $this->cacheMap->get($code);
    }

    /**
     * @inheritDoc
     */
    public function getId($code)
    {
        return $this->cacheMap->getId($code);
    }

    /**
     * @inheritDoc
     */
    public function clearCache(): void
    {
        $this->cacheMap->clearCache();
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return $this->cacheMap->getIterator();
    }
}

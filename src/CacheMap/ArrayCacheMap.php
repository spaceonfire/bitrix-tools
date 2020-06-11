<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

final class ArrayCacheMap extends AbstractCacheMap
{
    /**
     * Конструктор.
     * @param array $items
     * @param CacheMapOptions $options
     */
    public function __construct(array $items, CacheMapOptions $options)
    {
        $this->storage = $items;
        $this->options = $options;
    }

    protected function fillInner(): iterable
    {
        return [];
    }
}

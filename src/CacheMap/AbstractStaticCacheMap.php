<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

abstract class AbstractStaticCacheMap implements StaticCacheMap
{
    /**
     * @inheritDoc
     */
    public static function get($code)
    {
        return static::getInstance()->get($code);
    }

    /**
     * @inheritDoc
     */
    public static function getId($code)
    {
        return static::getInstance()->getId($code);
    }

    /**
     * @inheritDoc
     */
    public static function clearCache(): void
    {
        static::getInstance()->clearCache();
    }
}

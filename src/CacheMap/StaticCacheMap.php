<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

interface StaticCacheMap
{
    /**
     * Возвращает экземпляр внутреннего CacheMap
     * @return CacheMap
     */
    public static function getInstance(): CacheMap;

    /**
     * Возвращает элемент по коду
     * @param string|mixed $code
     * @return array|mixed|null
     */
    public static function get($code);

    /**
     * Возвращает ID элемента по коду
     * @param string|mixed $code
     * @return string|int|mixed|null
     */
    public static function getId($code);

    /**
     * Очистка кэша
     * @return void
     */
    public static function clearCache(): void;
}

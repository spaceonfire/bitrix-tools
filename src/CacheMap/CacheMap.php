<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

use IteratorAggregate;
use Traversable;

interface CacheMap extends IteratorAggregate
{
    /**
     * Возвращает данные элемента по символьному коду
     * @param string|mixed $code
     * @return array|mixed|null
     */
    public function get($code);

    /**
     * Возвращает ID элемента по символьному коду
     * @param string|mixed $code
     * @return string|int|mixed|null
     */
    public function getId($code);

    /**
     * Очистка кэша
     * @return void
     */
    public function clearCache(): void;

    /**
     * Возвращает итератор по элементам
     * @return Traversable
     */
    public function getIterator(): Traversable;
}

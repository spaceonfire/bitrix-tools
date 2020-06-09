<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main\ORM\Query\Query;
use InvalidArgumentException;

/**
 * Класс CustomCacheMap позволяет создать собственный кэшированный справочник
 * @package spaceonfire\BitrixTools\CacheMap
 */
final class CustomCacheMap extends AbstractCacheMapDecorator
{
    /**
     * Создает собственный кэшированный справочник на основе предоставленного источника данных.
     *
     * **ВАЖНО**: Позаботьтесь самостоятельно об очистке кэша, при изменении данных!
     *
     * @param Query|callable|array $dataSource Источник данных, можно передать объект запроса ORM, массив или функцию
     *     возвращающую `iterable`.
     * @param CacheMapOptions $options Настройки
     */
    public function __construct($dataSource, CacheMapOptions $options)
    {
        if ($dataSource instanceof Query) {
            $cacheMap = new QueryCacheMap($dataSource, $options);
        } elseif (is_callable($dataSource)) {
            $cacheMap = new ClosureCacheMap($dataSource, $options);
        } elseif (is_array($dataSource)) {
            $cacheMap = new ArrayCacheMap($dataSource, $options);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Argument "dataSource" should be a callable, an array or instance of %s. Got: %s',
                Query::class,
                gettype($dataSource)
            ));
        }

        parent::__construct($cacheMap);
    }
}

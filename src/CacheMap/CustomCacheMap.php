<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\ORM\Query\Query;

/**
 * Класс CustomCacheMap позволяет создать собственный кэшированный справочник
 * @package spaceonfire\BitrixTools\CacheMap
 *
 * @method array|null get(string $code) Возвращает данные элемента по символьному коду
 * @method int|mixed getId(string $code) Возвращает ID элемента по символьному коду
 * @method int|mixed clearCache() Очищает кэш
 */
final class CustomCacheMap implements CacheMapInterface
{
    use CacheMapTrait {
        CacheMapTrait::getDataByCode as get;
        CacheMapTrait::getIdByCode as getId;
        CacheMapTrait::getCacheOptions as traitGetCacheOptions;
        CacheMapTrait::traitClearCache as clearCache;
    }

    /**
     * Создает собственный кэшированный справочник на основе предоставленного источника данных.
     *
     * **ВАЖНО**: Позаботьтесь самостоятельно об очистке кэша, при изменении данных!
     *
     * @param Query|callable $dataSource Источник данных для справочника, можно передать объект запроса ORM или функцию
     *     возвращающую массив значений.
     * @param string $idKey Поле, принимаемое как идентификатор в справочнике. По-умолчанию, `ID`.
     * @param string $codeKey Поле, принимаемое как символьный код в справочнике. По-умолчанию, `CODE`.
     * @throws ArgumentTypeException
     */
    public function __construct($dataSource, $idKey = 'ID', $codeKey = 'CODE')
    {
        $this->traitConstruct($dataSource, $idKey, $codeKey);
    }

    private function getCacheOptions(): array
    {
        $options = $this->traitGetCacheOptions();

        $cacheId = substr(md5(serialize($this->query ?? $this->fillCallback)), 0, 10);

        $cachePath = explode(DIRECTORY_SEPARATOR, $options['CACHE_PATH']);
        array_pop($cachePath);
        $cachePath[] = $cacheId;
        $cachePath = implode(DIRECTORY_SEPARATOR, $cachePath);

        $options['CACHE_ID'] = $cacheId;
        $options['CACHE_PATH'] = $cachePath;

        return $options;
    }
}

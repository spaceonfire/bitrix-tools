<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main\ORM\Query\Query;

final class QueryCacheMap extends AbstractCacheMap
{
    /**
     * @var Query
     */
    private $query;

    /**
     * Конструктор.
     * @param Query $query
     * @param CacheMapOptions $options
     */
    public function __construct(Query $query, CacheMapOptions $options)
    {
        // Disable pagination and additional count query
        $query->setLimit(null)->setOffset(null)->countTotal(false);

        $this->query = $query;
        $this->options = $options;

        $this->fill();
    }

    protected function fillInner(): iterable
    {
        return $this->query->fetchAll();
    }
}

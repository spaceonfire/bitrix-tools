<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main;
use Bitrix\Main\ORM\Query\Query;
use Closure;
use Opis\Closure\SerializableClosure;
use spaceonfire\BitrixTools\Cache;

trait CacheMapTrait
{
    private $map = [];
    private $idKey;
    private $codeKey;
    private $fillCallback;
    private $query;
    private $isCaseSensitive = false;

    private function traitConstruct($dataSource, $idKey = 'ID', $codeKey = 'CODE'): void
    {
        if ($dataSource instanceof Query) {
            $this->query = $dataSource;
            $this->setFillCallback($this->fillWithQuery());
        } elseif (is_callable($dataSource)) {
            $this->setFillCallback($dataSource);
        } elseif (is_array($dataSource)) {
            $this->map = $dataSource;
        } else {
            throw new Main\ArgumentTypeException('dataSource', [Query::class, 'callable', 'array',]);
        }

        $this->idKey = $idKey;
        $this->codeKey = $codeKey;

        $this->fill();
    }

    private function setFillCallback(callable $callback): void
    {
        $this->fillCallback = new SerializableClosure(
            $callback instanceof Closure ? $callback : Closure::fromCallable($callback)
        );
    }

    private function fill(): void
    {
        if ($this->fillCallback === null) {
            return;
        }

        $data = Cache::cacheResult($this->getCacheOptions(), $this->fillCallback);

        if (!is_array($data)) {
            throw new Main\SystemException('fillCallback returned non-array result');
        }

        foreach ($data as $item) {
            if (
                !isset($item[$this->codeKey]) ||
                !is_string($item[$this->codeKey]) ||
                $item[$this->codeKey] === '' ||
                isset($this->map[$item[$this->codeKey]])
            ) {
                continue;
            }

            $code = $this->prepareCode($item[$this->codeKey]);
            $this->map[$code] = $item;
        }
    }

    private function getCacheOptions(): array
    {
        $cachePath = explode('\\', static::class);
        array_unshift($cachePath, '');

        $cacheId = array_pop($cachePath);

        $cachePath[] = $cacheId;
        $cachePath = implode(DIRECTORY_SEPARATOR, $cachePath);

        return [
            'CACHE_ID' => $cacheId,
            'CACHE_TAG' => null,
            'CACHE_PATH' => $cachePath,
        ];
    }

    private function fillWithQuery()
    {
        return function () {
            // Disable pagination and additional count query
            $this->query
                ->setLimit(null)
                ->setOffset(null)
                ->countTotal(false);

            return $this->query->fetchAll();
        };
    }

    /**
     * Возвращает данные элемента по символьному коду
     * @param string $code символьный код
     * @return array|null
     */
    public function getDataByCode($code): ?array
    {
        $code = $this->prepareCode($code);
        return $this->map[$code];
    }

    /**
     * Возвращает ID элемента по символьному коду
     * @param string $code символьный код
     * @return int|mixed ID элемента. По возможности будет приведен к целочисленному типу
     */
    public function getIdByCode($code)
    {
        $code = $this->prepareCode($code);

        if (!isset($this->map[$code][$this->idKey])) {
            return null;
        }

        if ((int)$this->map[$code][$this->idKey] . '' === $this->map[$code][$this->idKey] . '') {
            return (int)$this->map[$code][$this->idKey];
        }

        return $this->map[$code][$this->idKey];
    }

    /**
     * Очищает кэш
     * @throws Main\ArgumentNullException
     */
    public function traitClearCache(): void
    {
        Cache::clearCache($this->getCacheOptions());
    }

    /**
     * @return bool
     */
    public function isCaseSensitive(): bool
    {
        return $this->isCaseSensitive;
    }

    /**
     * @param bool $isCaseSensitive
     */
    public function setIsCaseSensitive(bool $isCaseSensitive): void
    {
        $this->isCaseSensitive = $isCaseSensitive;
    }

    private function prepareCode(string $code): string
    {
        return $this->isCaseSensitive() ? $code : strtolower($code);
    }
}

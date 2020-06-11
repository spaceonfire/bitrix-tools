<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

use ArrayIterator;
use spaceonfire\BitrixTools\Cache;
use spaceonfire\Collection\ArrayHelper;
use Traversable;
use Webmozart\Assert\Assert;

abstract class AbstractCacheMap implements CacheMap
{
    /**
     * @var mixed[]
     */
    protected $storage = [];
    /**
     * @var CacheMapOptions
     */
    protected $options;

    final protected function fill(): void
    {
        $data = Cache::cacheResult($this->options->getCacheOptions(), function () {
            return $this->fillInner();
        });

        foreach ($data as $item) {
            $code = $this->prepareCode(ArrayHelper::getValue($item, $this->options->getCodeKey(), ''));

            if ($code === '' || isset($this->map[$code])) {
                continue;
            }

            $this->storage[$code] = $item;
        }
    }

    abstract protected function fillInner(): iterable;

    private function prepareCode($code): string
    {
        Assert::scalar($code);
        $code = (string)$code;
        return $this->options->isCaseSensitive() ? $code : mb_strtolower($code);
    }

    /**
     * @inheritDoc
     */
    public function get($code)
    {
        $code = $this->prepareCode($code);
        return $this->storage[$code] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getId($code)
    {
        $code = $this->prepareCode($code);

        if (!isset($this->storage[$code])) {
            return null;
        }

        $id = ArrayHelper::getValue($this->storage[$code], $this->options->getIdKey());

        /** @noinspection TypeUnsafeComparisonInspection */
        if (is_numeric($id) && $id == (int)$id) {
            return (int)$id;
        }

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function clearCache(): void
    {
        Cache::clearCache($this->options->getCacheOptions());
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->storage);
    }
}

<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

use RuntimeException;

final class ClosureCacheMap extends AbstractCacheMap
{
    /**
     * @var callable
     */
    private $fillCallback;

    /**
     * Конструктор.
     * @param callable $fillCallback
     * @param CacheMapOptions $options
     */
    public function __construct(callable $fillCallback, CacheMapOptions $options)
    {
        $this->fillCallback = $fillCallback;
        $this->options = $options;

        $this->fill();
    }

    protected function fillInner(): iterable
    {
        $data = ($this->fillCallback)();

        if (!is_iterable($data)) {
            throw new RuntimeException(sprintf(
                'Expected return value by `fillCallback` to be an iterable. Got "%s"',
                gettype($data)
            ));
        }

        return $data;
    }
}

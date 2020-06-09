<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

final class CacheMapOptions
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $idKey;
    /**
     * @var string
     */
    private $codeKey;
    /**
     * @var bool
     */
    private $isCaseSensitive;
    /**
     * @var array|null
     */
    private $cacheOptions;

    /**
     * CacheMapOptions constructor.
     * @param string $id
     * @param string $idKey
     * @param string $codeKey
     * @param bool $isCaseSensitive
     * @param array $cacheOptions
     */
    public function __construct(
        string $id,
        string $idKey = 'ID',
        string $codeKey = 'CODE',
        bool $isCaseSensitive = false,
        ?array $cacheOptions = null
    ) {
        $this->id = $id;
        $this->idKey = $idKey;
        $this->codeKey = $codeKey;
        $this->isCaseSensitive = $isCaseSensitive;
        $this->cacheOptions = $cacheOptions;
    }

    /**
     * Getter for `idKey` property
     * @return string
     */
    public function getIdKey(): string
    {
        return $this->idKey;
    }

    /**
     * Getter for `codeKey` property
     * @return string
     */
    public function getCodeKey(): string
    {
        return $this->codeKey;
    }

    /**
     * Getter for `isCaseSensitive` property
     * @return bool
     */
    public function isCaseSensitive(): bool
    {
        return $this->isCaseSensitive;
    }

    /**
     * Getter for `cacheOptions` property
     * @return array
     */
    public function getCacheOptions(): array
    {
        if ($this->cacheOptions === null) {
            return [
                'CACHE_ID' => $this->id,
                'CACHE_TAG' => null,
                'CACHE_PATH' => 'bitrix-tools/cache-map/' . $this->id,
            ];
        }

        return $this->cacheOptions;
    }
}

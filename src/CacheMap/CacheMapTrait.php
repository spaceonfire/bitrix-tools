<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main;
use Bitrix\Main\ORM\Query\Query;
use spaceonfire\BitrixTools\Cache;
use SuperClosure\Analyzer\TokenAnalyzer;
use SuperClosure\SerializableClosure;
use SuperClosure\Serializer;

trait CacheMapTrait
{
	private $map = [];
	private $idKey;
	private $codeKey;
	private $fillCallback;
	private $query;

	private function traitConstruct($dataSource, $idKey = 'ID', $codeKey = 'CODE')
	{
		if ($dataSource instanceof Query) {
			$this->query = $dataSource;
			$this->setFillCallback($this->fillWithQuery());
		} else if (is_callable($dataSource)) {
			$this->setFillCallback($dataSource);
		} else if (is_array($dataSource)) {
			$this->map = $dataSource;
		} else {
			throw new Main\ArgumentTypeException('dataSource', [
				Query::class,
				'callable',
				'array',
			]);
		}

		$this->idKey = $idKey;
		$this->codeKey = $codeKey;

		$this->fill();
	}

	private function setFillCallback(callable $callback)
	{
		$this->fillCallback = new SerializableClosure(
			$callback instanceof \Closure ? $callback : \Closure::fromCallable($callback),
			new Serializer(new TokenAnalyzer())
		);
	}

	private function fill(): void
	{
		if ($this->fillCallback === null) {
			return;
		}

		$data = Cache::cacheResult(
			$this->getCacheOptions(),
			$this->fillCallback
		);

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

			$this->map[$item[$this->codeKey]] = $item;
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

	public function getDataByCode($code): ?array
	{
		return $this->map[$code];
	}

	public function getIdByCode($code)
	{
		if (!isset($this->map[$code][$this->idKey])) {
			return null;
		}

		if ((int)$this->map[$code][$this->idKey] . '' === $this->map[$code][$this->idKey] . '') {
			return (int)$this->map[$code][$this->idKey];
		}

		return $this->map[$code][$this->idKey];
	}

	public function traitClearCache()
	{
		Cache::clearCache($this->getCacheOptions());
	}
}

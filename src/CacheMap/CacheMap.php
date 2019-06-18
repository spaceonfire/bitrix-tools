<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main;
use Bitrix\Main\ORM\Query\Query;
use spaceonfire\BitrixTools\Cache;

class CacheMap
{
	private $map = [];
	private $idKey;
	private $codeKey;
	private $fillCallback;
	private $query;

	public function __construct($dataSource, $idKey = 'ID', $codeKey = 'CODE')
	{
		if ($dataSource instanceof Query) {
			$this->query = $dataSource;
			$this->fillCallback = [$this, 'fillWithQuery'];
		} else if (is_callable($dataSource)) {
			$this->fillCallback = $dataSource;
		} else if (is_array($dataSource)) {
			$this->map = $dataSource;
		} else {
			throw new Main\ArgumentTypeException('dataSource', [
				'Bitrix\Main\ORM\Query\Query',
				'callable',
				'array',
			]);
		}

		$this->idKey = $idKey;
		$this->codeKey = $codeKey;

		$this->fill();
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

	protected function getCacheOptions(): array
	{
		$cachePath = explode('\\', static::class);
		array_unshift($cachePath, '');

		if (static::class === __CLASS__) {
			$cacheId = substr(md5(serialize($this->query ?? $this->fillCallback)), 0, 10);
		} else {
			$cacheId = array_pop($cachePath);
		}

		array_push($cachePath, $cacheId);
		$cachePath = implode(DIRECTORY_SEPARATOR, $cachePath);

		return [
			'CACHE_ID' => $cacheId,
			'CACHE_TAG' => null,
			'CACHE_PATH' => $cachePath,
		];
	}

	public function fillWithQuery(): array
	{
		// Disable pagination and additional count query
		$this->query
			->setLimit(null)
			->setOffset(null)
			->countTotal(false);

		return $this->query->fetchAll();
	}

	public function getDataByCode($code): array
	{
		return $this->map[$code];
	}

	public function getIdByCode($code)
	{
		return $this->map[$code][$this->idKey];
	}
}

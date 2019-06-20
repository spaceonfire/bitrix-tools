<?php

namespace spaceonfire\BitrixTools\CacheMap;

final class CustomCacheMap implements CacheMapInterface
{
	use CacheMapTrait {
		CacheMapTrait::getDataByCode as get;
		CacheMapTrait::getIdByCode as getId;
		CacheMapTrait::getCacheOptions as traitGetCacheOptions;
		CacheMapTrait::traitClearCache as clearCache;
	}

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

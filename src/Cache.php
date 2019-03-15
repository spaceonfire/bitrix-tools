<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Data\Cache as BxCache;

class Cache
{
	/**
	 * Cache $callback results using bitrix Cache
	 * @param array $options cache params. An array with keys CACHE_ID, CACHE_TAG, CACHE_PATH, CACHE_TIME
	 * @param callable $callback function to cache results
	 * @param array $args array of arguments for $callback function
	 * @return mixed
	 * @throws ArgumentNullException
	 */
	public static function cacheResult($options, callable $callback, $args = [])
	{
		$options = array_merge([
			'CACHE_ID' => null,
			'CACHE_TAG' => null,
			'CACHE_PATH' => null,
			'CACHE_TIME' => 36000000,
		], $options);

		foreach (['CACHE_ID', 'CACHE_PATH'] as $sParam) {
			if (empty($options[$sParam])) {
				throw new ArgumentNullException($sParam);
			}
		}

		$ds = DIRECTORY_SEPARATOR;
		if (!empty($options['CACHE_TAG'])) {
			$options['CACHE_PATH'] .= $ds . $options['CACHE_TAG'];
		}
		$siteId = defined('ADMIN_SECTION') && ADMIN_SECTION ? 's1' : SITE_ID;
		$options['CACHE_PATH'] = str_replace($ds . $ds, $ds, $ds . $siteId . $ds . $options['CACHE_PATH']);
		unset($ds);

		if (!empty($args)) {
			$options['CACHE_ID'] = $options['CACHE_ID'] . ':' . substr(md5(serialize($args)), 0, 10);
		}

		$obCache = BxCache::createInstance();
		$result = null;

		if ($obCache->initCache($options['CACHE_TIME'], $options['CACHE_ID'], $options['CACHE_PATH'])) {
			$result = $obCache->getVars();
		} elseif ($obCache->startDataCache()) {
			$result = call_user_func_array($callback, $args);

			if (!empty($result)) {
				if (defined('BX_COMP_MANAGED_CACHE') && !empty($options['CACHE_TAG'])) {
					global $CACHE_MANAGER;
					$CACHE_MANAGER->StartTagCache($options['CACHE_PATH']);
					$CACHE_MANAGER->RegisterTag($options['CACHE_TAG']);
					$CACHE_MANAGER->EndTagCache();
				}

				$obCache->endDataCache($result);
			} else {
				$obCache->abortDataCache();
			}
		}

		return $result;
	}
}

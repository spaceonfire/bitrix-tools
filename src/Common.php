<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main;

class Common
{
	/**
	 * Load modules
	 * @param array $modules an array of modules to load
	 * @throws Main\LoaderException
	 */
	public static function loadModules(array $modules): void
	{
		foreach ($modules as $module) {
			if (!Main\Loader::includeModule($module)) {
				throw new Main\LoaderException('Could not load ' . $module . ' module');
			}
		}
	}
}
